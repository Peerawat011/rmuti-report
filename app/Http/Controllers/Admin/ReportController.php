<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * รายงานทั้งหมดของทุกคน — ค้นหา + กรองสถานะ
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $status = $request->input('status', '');

        $reports = Report::query()
            ->with('user')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('topic', 'like', "%{$search}%")
                        ->orWhere('reporter_name', 'like', "%{$search}%")
                        ->orWhere('order_number', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['draft', 'pending_signature', 'signed', 'approved', 'revision'], true),
                fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact('reports', 'search', 'status'));
    }

    /**
     * ฟอร์มสร้างรายงานใหม่ให้ผู้ใช้
     */
    public function create()
    {
        return view('admin.reports.create', [
            'report'      => null,
            'users'       => $this->reporterOptions(),
            'supervisors' => $this->supervisorOptions(),
        ]);
    }

    /**
     * บันทึกรายงานใหม่ (กำหนดเจ้าของได้)
     */
    public function store(Request $request)
    {
        $validated = $this->validateReport($request);

        $owner = User::findOrFail($validated['user_id']);
        $validated = array_merge($validated, $this->reporterFields($owner));
        $validated['doc_number'] = \App\Services\DocumentNumber::next('training');
        $validated['status']     = 'draft';
        $validated['total_days'] = $this->calculateDays($validated['start_date'], $validated['end_date']);

        $report = Report::create($validated);

        return redirect()->route('admin.reports.index')
            ->with('success', 'สร้างรายงานให้ ' . $owner->full_name . ' เรียบร้อยแล้ว');
    }

    /**
     * ฟอร์มแก้ไขรายงาน
     */
    public function edit(Report $report)
    {
        return view('admin.reports.edit', [
            'report'      => $report,
            'users'       => $this->reporterOptions(),
            'supervisors' => $this->supervisorOptions(),
        ]);
    }

    /**
     * อัปเดตรายงาน (เปลี่ยนเจ้าของได้ → อัปเดตข้อมูลผู้รายงานตามเจ้าของใหม่)
     */
    public function update(Request $request, Report $report)
    {
        $validated = $this->validateReport($request);

        $owner = User::findOrFail($validated['user_id']);
        $validated = array_merge($validated, $this->reporterFields($owner));
        $validated['total_days'] = $this->calculateDays($validated['start_date'], $validated['end_date']);

        $report->update($validated);

        return redirect()->route('admin.reports.index')
            ->with('success', 'แก้ไขรายงานเรียบร้อยแล้ว');
    }

    /**
     * ลบรายงาน
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'ลบรายงานเรียบร้อยแล้ว');
    }

    /** รายชื่อผู้ใช้สำหรับเลือกเป็นเจ้าของรายงาน */
    private function reporterOptions()
    {
        return User::orderBy('first_name')->get();
    }

    /** รายชื่อผู้บังคับบัญชา */
    private function supervisorOptions()
    {
        return User::where('role', 'supervisor')->orderBy('first_name')->get();
    }

    /** ดึงข้อมูลผู้รายงานจาก user ที่เลือก */
    private function reporterFields(User $owner): array
    {
        return [
            'user_id'             => $owner->id,
            'reporter_name'       => $owner->first_name . ' ' . $owner->last_name,
            'reporter_position'   => $owner->position,
            'reporter_department' => $owner->department,
            'reporter_faculty'    => $owner->faculty,
        ];
    }

    private function calculateDays($start, $end)
    {
        return Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;
    }

    private function validateReport(Request $request): array
    {
        return $request->validate([
            'user_id'         => 'required|exists:users,id',
            'activity_type'   => 'required|in:อบรม,ศึกษาดูงาน,ประชุมสัมมนา',
            'topic'           => 'required|string|max:500',
            'order_number'    => 'nullable|string|max:255',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'location'        => 'required|string',
            'organizer'       => 'required|string|max:255',
            'usage_types'     => 'nullable|array',
            'usage_other'     => 'nullable|string|max:500',
            'documents'       => 'nullable|string',
            'details'         => 'nullable|string',
            'suggestions'     => 'nullable|string',
            'supervisor_1_id' => 'required|exists:users,id|different:supervisor_2_id|different:supervisor_3_id',
            'supervisor_2_id' => 'nullable|exists:users,id|different:supervisor_3_id',
            'supervisor_3_id' => 'nullable|exists:users,id',
        ], [
            'user_id.required'          => 'กรุณาเลือกเจ้าของรายงาน',
            'user_id.exists'            => 'ไม่พบผู้ใช้ที่เลือก',
            'topic.required'            => 'กรุณากรอกหัวข้อเรื่อง',
            'start_date.required'       => 'กรุณาเลือกวันที่เริ่ม',
            'end_date.after_or_equal'   => 'วันที่สิ้นสุดต้องไม่ก่อนวันที่เริ่ม',
            'supervisor_1_id.required'  => 'กรุณาเลือกผู้บังคับบัญชาลำดับที่ 1',
            'supervisor_1_id.different' => 'ผู้บังคับบัญชาแต่ละลำดับต้องเป็นคนละคน',
            'supervisor_2_id.different' => 'ผู้บังคับบัญชาแต่ละลำดับต้องเป็นคนละคน',
        ]);
    }
}
