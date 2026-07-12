<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    // ========== หน้าลงนาม ==========
    public function create(Report $report)
    {
        // ตรวจสิทธิ์ (เป็นเจ้าของรายงานเท่านั้น)
        if ($report->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้');
        }

        // เช็คว่าลงนามแล้วหรือยัง
        $existingSignature = $report->signatures()->where('role', 'reporter')->first();

        return view('reports.sign', compact('report', 'existingSignature'));
    }

    // ========== บันทึกลายเซ็น ==========
    public function store(Request $request, Report $report)
    {
        // ตรวจสิทธิ์
        if ($report->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้');
        }

        // Validate ข้อมูล (profile = ใช้ลายเซ็นประจำตัวที่ตั้งไว้)
        $request->validate([
            'signature_mode' => 'required|in:profile,draw,upload',
            'signature_data' => 'required_if:signature_mode,draw|nullable|string',
            'signature_file' => 'required_if:signature_mode,upload|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signed_date'    => 'required|date',
        ], [
            'signature_data.required_if' => 'กรุณาวาดลายเซ็น',
            'signature_file.required_if' => 'กรุณาเลือกรูปลายเซ็น',
            'signature_file.image'       => 'ไฟล์ต้องเป็นรูปภาพ',
            'signature_file.mimes'       => 'รองรับเฉพาะ JPG, PNG',
            'signature_file.max'         => 'ขนาดไฟล์ต้องไม่เกิน 2 MB',
            'signed_date.required'       => 'กรุณาเลือกวันที่ลงนาม',
        ]);

        $user = Auth::user();

        // หาลายเซ็นประจำตัวที่จะใช้: ใช้เวอร์ชันล่าสุด หรือสร้างเวอร์ชันใหม่จากที่วาด/อัปโหลด
        if ($request->signature_mode === 'profile') {
            $userSignature = $user->activeSignature;
            if (!$userSignature) {
                return back()->withErrors(['signature_mode' => 'คุณยังไม่มีลายเซ็นประจำตัว กรุณาวาดหรืออัปโหลดลายเซ็นก่อน']);
            }
        } else {
            $userSignature = UserSignatureController::createVersion($request, $user->id);
        }

        // ลบลายเซ็นเก่าของรายงานนี้ (ถ้ามี) - สำหรับ flow แก้ไขลายเซ็น
        $existingSignature = $report->signatures()->where('role', 'reporter')->first();
        if ($existingSignature) {
            $this->deleteSignatureRecord($existingSignature);
        }

        // บันทึกลง database — อ้างอิงเวอร์ชันลายเซ็นประจำตัว (ไม่ก็อปไฟล์ต่อรายงาน)
        Signature::create([
            'report_id'         => $report->id,
            'role'              => 'reporter',
            'signer_name'       => $user->first_name . ' ' . $user->last_name,
            'signer_position'   => $user->position,
            'user_signature_id' => $userSignature->id,
            'signed_date'       => $request->signed_date,
        ]);

        // อัปเดตสถานะรายงานเป็น "ลงนามแล้ว"
        $report->update(['status' => 'signed']);

        return redirect()->route('reports.show', $report)
            ->with('success', 'ลงนามรายงานเรียบร้อยแล้ว');
    }

    // ========== Helper: ลบ record ลายเซ็น (ลบไฟล์เฉพาะแบบเก่าที่เป็นไฟล์ต่อรายงาน) ==========
    private function deleteSignatureRecord(Signature $signature): void
    {
        // แบบเก่า: ไฟล์ต่อรายงาน → ลบไฟล์ได้ / แบบใหม่: อ้างเวอร์ชันประจำตัว → ห้ามลบไฟล์ (ใช้ร่วมกับเอกสารอื่น)
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

    // ========== ลบลายเซ็น ==========
    public function destroy(Report $report, Signature $signature)
    {
        // signature ต้องเป็นของรายงานนี้จริง
        if ($signature->report_id !== $report->id) {
            abort(403);
        }

        // สิทธิ์: admin ลบได้ทุกลายเซ็น / เจ้าของรายงานลบได้เฉพาะลายเซ็นผู้รายงานของตัวเอง
        $user = Auth::user();
        $isOwnerReporterSig = $report->user_id === $user->id && $signature->role === 'reporter';

        if (!$user->isAdmin() && !$isOwnerReporterSig) {
            abort(403, 'คุณไม่มีสิทธิ์ลบลายเซ็นนี้');
        }

        $this->deleteSignatureRecord($signature);

        // ปรับสถานะรายงานให้สอดคล้อง
        if ($signature->role === 'reporter') {
            // ลบลายเซ็นผู้รายงาน → กลับเป็นร่าง (เริ่ม workflow ใหม่)
            $report->update(['status' => 'draft']);
        } elseif ($report->status === 'approved') {
            // ลบลายเซ็นผู้บังคับบัญชาจากรายงานที่อนุมัติแล้ว → เซ็นไม่ครบ ถอยเป็น "ลงนามแล้ว"
            $report->update(['status' => 'signed']);
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'ลบลายเซ็นเรียบร้อยแล้ว');
    }

}