<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // ========== หน้ารายการรายงานของฉัน ==========
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    // ========== หน้าสร้างรายงานใหม่ ==========
    public function create()
    {
        $user = Auth::user();
        $supervisors = \App\Models\User::where('role', 'supervisor')
        ->orderBy('first_name')
        ->get();
        return view('reports.create', compact('user', 'supervisors'));
    }

    // ========== บันทึกรายงานใหม่ ==========
    public function store(Request $request)
    {
        $validated = $this->validateReport($request);

        $user = Auth::user();
        $validated['user_id']            = $user->id;
        $validated['reporter_name']      = $user->first_name . ' ' . $user->last_name;
        $validated['reporter_position']  = $user->position;
        $validated['reporter_department'] = $user->department;
        $validated['reporter_faculty']   = $user->faculty;
        $validated['status']             = 'draft';

        // คำนวณจำนวนวันอัตโนมัติ
        $validated['total_days'] = $this->calculateDays(
            $validated['start_date'],
            $validated['end_date']
        );

        $report = Report::create($validated);

        return redirect()->route('reports.show', $report)
            ->with('success', 'สร้างรายงานเรียบร้อย คุณสามารถลงนามได้ในขั้นถัดไป');
    }

    // ========== หน้าดูรายงาน ==========
    public function show(Report $report)
    {
        
        $this->authorize_report($report, 'view');   // ← เพิ่ม 'view'
        $report->load('signatures');
        return view('reports.show', compact('report'));
    }

    // ========== หน้าแก้ไขรายงาน ==========
    public function edit(Report $report)
    {
        $this->authorize_report($report, 'edit');
        $supervisors = \App\Models\User::where('role', 'supervisor')
        ->orderBy('first_name')
        ->get();
        return view('reports.edit', compact('report', 'supervisors'));
    }

    // ========== บันทึกการแก้ไข ==========
    public function update(Request $request, Report $report)
    {
        $this->authorize_report($report, 'edit');

        $validated = $this->validateReport($request);
        $validated['total_days'] = $this->calculateDays(
            $validated['start_date'],
            $validated['end_date']
        );

        $report->update($validated);

        return redirect()->route('reports.show', $report)
            ->with('success', 'แก้ไขรายงานเรียบร้อยแล้ว');
    }

    // ========== ลบรายงาน ==========
    public function destroy(Report $report)
    {
        $this->authorize_report($report, 'delete');
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'ลบรายงานเรียบร้อยแล้ว');
    }

        // ========== Helper: ตรวจสิทธิ์ ==========
        private function authorize_report(Report $report, $action = 'view')
    {
        $userId = Auth::id();

        // ผู้ดูแลระบบ → เข้าถึงได้ทุกรายงาน
        if (Auth::user()->isAdmin()) {
            return true;
        }

        // เจ้าของรายงาน → ทำได้ทุกอย่าง
        if ($report->user_id === $userId) {
            return true;
        }

        // ถ้าเป็น supervisor ของรายงานนี้ → ดูได้อย่างเดียว
        if ($action === 'view') {
            $isSupervisor = in_array($userId, [
                $report->supervisor_1_id,
                $report->supervisor_2_id,
                $report->supervisor_3_id,
            ]);

            if ($isSupervisor) {
                return true;
            }
        }

        // ไม่ตรงทุกกรณี → ห้าม
        abort(403, 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้');
    }

    // ========== Helper: คำนวณจำนวนวัน ==========
    private function calculateDays($start, $end)
    {
        return \Carbon\Carbon::parse($start)
            ->diffInDays(\Carbon\Carbon::parse($end)) + 1;
    }

    // ========== Helper: Validation ==========
    private function validateReport(Request $request)
    {
            return $request->validate([
            'activity_type' => 'required|in:อบรม,ศึกษาดูงาน,ประชุมสัมมนา',
            'topic'         => 'required|string|max:500',
            'order_number'  => 'nullable|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'location'      => 'required|string',
            'organizer'     => 'required|string|max:255',
            'usage_types'   => 'nullable|array',
            'usage_other'   => 'nullable|string|max:500',
            'documents'     => 'nullable|string',
            'details'       => 'nullable|string',
            'suggestions'   => 'nullable|string',
            // ===== Supervisor (ใหม่) =====
            'supervisor_1_id' => 'required|exists:users,id|different:supervisor_2_id|different:supervisor_3_id',
            'supervisor_2_id' => 'nullable|exists:users,id|different:supervisor_3_id',
            'supervisor_3_id' => 'nullable|exists:users,id',
        ], [
            // ... ข้อความเดิม ...
            'supervisor_1_id.required' => 'กรุณาเลือกผู้บังคับบัญชาลำดับที่ 1',
            'supervisor_1_id.different' => 'ผู้บังคับบัญชาแต่ละลำดับต้องเป็นคนละคน',
            'supervisor_2_id.different' => 'ผู้บังคับบัญชาแต่ละลำดับต้องเป็นคนละคน',
            // ... ข้อความเดิม ...
        ]);
    }

    // ========== Export PDF ==========
    public function downloadPDF(Report $report)
    {
        // ตรวจสิทธิ์ (เหมือน show)
        $this->authorize_report($report, 'view');

        // โหลดข้อมูล signatures มาด้วย
        $report->load('signatures', 'supervisor1', 'supervisor2', 'supervisor3');

        // สร้าง PDF
        $pdf = Pdf::loadView('reports.pdf', compact('report'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'thsarabun',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        // ชื่อไฟล์
        $filename = 'แบบรายงานการพัฒนาบุคลากรโดยการอบรมศึกษาดูงานประชุมสัมมนา.pdf';

        return $pdf->download($filename);
    }
}