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

        // Validate (profile = ใช้ลายเซ็นประจำตัวที่ตั้งไว้)
        $request->validate([
            'comment'        => 'nullable|string|max:1000',
            'signature_mode' => 'required|in:profile,draw,upload',
            'signature_data' => 'required_if:signature_mode,draw|nullable|string',
            'signature_file' => 'required_if:signature_mode,upload|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signed_date'    => 'required|date',
        ], [
            'signature_data.required_if' => 'กรุณาวาดลายเซ็น',
            'signature_file.required_if' => 'กรุณาเลือกรูปลายเซ็น',
            'signed_date.required'       => 'กรุณาเลือกวันที่ลงนาม',
        ]);

        $user = Auth::user();

        // หาลายเซ็นประจำตัวที่จะใช้: เวอร์ชันล่าสุด หรือสร้างเวอร์ชันใหม่จากที่วาด/อัปโหลด
        if ($request->signature_mode === 'profile') {
            $userSignature = $user->activeSignature;
            if (!$userSignature) {
                return back()->withErrors(['signature_mode' => 'คุณยังไม่มีลายเซ็นประจำตัว กรุณาวาดหรืออัปโหลดลายเซ็นก่อน']);
            }
        } else {
            $userSignature = \App\Http\Controllers\UserSignatureController::createVersion($request, $user->id);
        }

        // บันทึกลง database — อ้างอิงเวอร์ชันลายเซ็นประจำตัว (ไม่ก็อปไฟล์ต่อรายงาน)
        Signature::create([
            'report_id'         => $report->id,
            'role'              => 'supervisor_' . $role,
            'signer_name'       => $user->first_name . ' ' . $user->last_name,
            'signer_position'   => $user->position,
            'comment'           => $request->comment,
            'user_signature_id' => $userSignature->id,
            'signed_date'       => $request->signed_date,
        ]);

        // บันทึกเวลาที่ผู้บังคับบัญชาลงนามล่าสุด (ใช้แจ้งเตือนเจ้าของรายงาน)
        $report->update(['last_signed_at' => now()]);

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

    // ========== ตีกลับให้แก้ไข (พร้อมเหตุผล) ==========
    public function reject(Request $request, Report $report)
    {
        $userId = Auth::id();
        $role = $report->getSupervisorRoleFor($userId);

        if (!$role) {
            abort(403, 'คุณไม่มีสิทธิ์ดำเนินการกับรายงานนี้');
        }

        // ตีกลับได้เฉพาะตอนที่ถึงคิวตัวเองพิจารณา (เงื่อนไขเดียวกับการลงนาม)
        if (!$report->canSign($userId)) {
            return redirect()->route('reports.show', $report)
                ->with('error', $report->whyCantSign($userId));
        }

        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ], [
            'reject_reason.required' => 'กรุณาระบุเหตุผลที่ส่งกลับแก้ไข',
            'reject_reason.max'      => 'เหตุผลต้องไม่เกิน 1,000 ตัวอักษร',
        ]);

        // ลบลายเซ็นทั้งหมด (เนื้อหาจะถูกแก้ — ลายเซ็นเดิมใช้ไม่ได้แล้ว ต้องลงนามใหม่)
        // ลบไฟล์เฉพาะแบบเก่าที่เป็นไฟล์ต่อรายงาน — แบบอ้างเวอร์ชันประจำตัวห้ามลบไฟล์ (ใช้ร่วมกับเอกสารอื่น)
        foreach ($report->signatures as $signature) {
            if ($signature->signature_image && !$signature->user_signature_id) {
                \App\Services\FileStore::delete($signature->signature_image);
                foreach (['local', 'public'] as $disk) {
                    if (Storage::disk($disk)->exists($signature->signature_image)) {
                        Storage::disk($disk)->delete($signature->signature_image);
                    }
                }
            }
            $signature->delete();
        }

        $report->update([
            'status'          => 'revision',
            'revision_reason' => $request->reject_reason,
            'revision_by'     => $userId,
            'revision_at'     => now(),
            'last_signed_at'  => null,   // ลายเซ็นถูกลบหมดแล้ว — ล้างสถานะ "ถูกลงนาม"
        ]);

        return redirect()->route('supervisor.inbox')
            ->with('success', 'ส่งรายงานกลับให้ผู้รายงานแก้ไขเรียบร้อยแล้ว');
    }

}