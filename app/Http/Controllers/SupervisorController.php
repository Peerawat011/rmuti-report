<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupervisorController extends Controller
{
    // ========== หน้า Inbox ของ Supervisor ==========
    public function inbox()
    {
        $userId = Auth::id();

        $reports = Report::where(function ($q) use ($userId) {
                $q->where('supervisor_1_id', $userId)
                  ->orWhere('supervisor_2_id', $userId)
                  ->orWhere('supervisor_3_id', $userId);
            })
            ->where('status', '!=', 'draft')
            ->with(['user', 'signatures'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('supervisor.inbox', compact('reports'));
    }

    // ========== หน้าฟอร์มลงนาม ==========
    public function signForm(Report $report)
    {
        $userId = Auth::id();

        // ตรวจสิทธิ์: ต้องเป็น supervisor ของ report นี้
        $role = $report->getSupervisorRoleFor($userId);
        if (!$role) {
            abort(403, 'คุณไม่มีสิทธิ์ลงนามรายงานนี้');
        }

        // ตรวจ workflow
        if (!$report->canSign($userId)) {
            return redirect()->route('reports.show', $report)
                ->with('error', $report->whyCantSign($userId));
        }

        return view('supervisor.sign', compact('report', 'role'));
    }

    // ========== บันทึกลายเซ็น Supervisor ==========
    public function sign(Request $request, Report $report)
    {
        $userId = Auth::id();
        $role = $report->getSupervisorRoleFor($userId);

        if (!$role) {
            abort(403);
        }

        if (!$report->canSign($userId)) {
            return redirect()->route('reports.show', $report)
                ->with('error', $report->whyCantSign($userId));
        }

        // Validate
        $request->validate([
            'comment'        => 'nullable|string|max:1000',          // ← เปลี่ยน required เป็น nullable
            'signature_mode' => 'required|in:draw,upload',
            'signature_data' => 'required_if:signature_mode,draw|nullable|string',
            'signature_file' => 'required_if:signature_mode,upload|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signed_date'    => 'required|date',
        ], [
            'signature_data.required_if' => 'กรุณาวาดลายเซ็น',
            'signature_file.required_if' => 'กรุณาเลือกรูปลายเซ็น',
            'signed_date.required'       => 'กรุณาเลือกวันที่ลงนาม',
        ]);

        $user = Auth::user();

        // บันทึกรูปลายเซ็น
        if ($request->signature_mode === 'draw') {
            $signaturePath = $this->saveDrawnSignature(
                $request->signature_data,
                $report->id,
                $role
            );
        } else {
            $file = $request->file('signature_file');
            $filename = 'signature_sup' . $role . '_' . $report->id . '_' . time()
                      . '.' . $file->getClientOriginalExtension();
            $signaturePath = $file->storeAs('signatures', $filename, 'public');
        }

        // บันทึกลง database
        Signature::create([
            'report_id'       => $report->id,
            'role'            => 'supervisor_' . $role,
            'signer_name'     => $user->first_name . ' ' . $user->last_name,
            'signer_position' => $user->position,
            'comment'         => $request->comment,
            'signature_image' => $signaturePath,
            'signed_date'     => $request->signed_date,
        ]);

        // เช็คว่าทุก supervisor ที่เลือกไว้เซ็นครบหรือยัง
        $report->refresh(); // โหลดข้อมูลใหม่หลังเพิ่ม signature

        $expectedSignatures = collect([
            $report->supervisor_1_id ? 'supervisor_1' : null,
            $report->supervisor_2_id ? 'supervisor_2' : null,
            $report->supervisor_3_id ? 'supervisor_3' : null,
        ])->filter();

        $signedCount = $report->signatures()
            ->whereIn('role', $expectedSignatures->toArray())
            ->count();

        if ($signedCount === $expectedSignatures->count()) {
            $report->update(['status' => 'approved']);
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'ลงนามอนุมัติเรียบร้อยแล้ว (ลำดับที่ ' . $role . ')');
    }

    // ========== Helper: บันทึก base64 → file ==========
    private function saveDrawnSignature($base64Data, $reportId, $role)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            $extension = $matches[1];
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        } else {
            $extension = 'png';
        }

        $imageData = base64_decode($base64Data);
        $filename = 'signature_sup' . $role . '_' . $reportId . '_' . time() . '.' . $extension;
        $path = 'signatures/' . $filename;

        Storage::disk('public')->put($path, $imageData);

        return $path;
    }
}