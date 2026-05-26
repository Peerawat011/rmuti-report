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

        // Validate ข้อมูล
        $request->validate([
            'signature_mode' => 'required|in:draw,upload',
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

        // ลบลายเซ็นเก่า (ถ้ามี) - สำหรับ flow แก้ไขลายเซ็น
        $existingSignature = $report->signatures()->where('role', 'reporter')->first();
        if ($existingSignature) {
            if (Storage::disk('public')->exists($existingSignature->signature_image)) {
                Storage::disk('public')->delete($existingSignature->signature_image);
            }
            $existingSignature->delete();
        }

        // บันทึกรูปลายเซ็นตาม mode
        $signaturePath = null;

        if ($request->signature_mode === 'draw') {
            // Mode 1: วาด - มาเป็น base64 ต้อง decode
            $signaturePath = $this->saveDrawnSignature(
                $request->signature_data,
                $report->id,
                $user->id
            );
        } else {
            // Mode 2: อัปโหลด
            $file = $request->file('signature_file');
            $filename = 'signature_reporter_' . $report->id . '_' . time()
                      . '.' . $file->getClientOriginalExtension();
            $signaturePath = $file->storeAs('signatures', $filename, 'public');
        }

        // บันทึกลง database
        Signature::create([
            'report_id'       => $report->id,
            'role'            => 'reporter',
            'signer_name'     => $user->first_name . ' ' . $user->last_name,
            'signer_position' => $user->position,
            'signature_image' => $signaturePath,
            'signed_date'     => $request->signed_date,
        ]);

        // อัปเดตสถานะรายงานเป็น "ลงนามแล้ว"
        $report->update(['status' => 'signed']);

        return redirect()->route('reports.show', $report)
            ->with('success', 'ลงนามรายงานเรียบร้อยแล้ว');
    }

    // ========== ลบลายเซ็น ==========
    public function destroy(Report $report, Signature $signature)
    {
        // ตรวจสิทธิ์
        if ($report->user_id !== Auth::id() || $signature->report_id !== $report->id) {
            abort(403);
        }

        // ลบไฟล์รูป
        if ($signature->signature_image && Storage::disk('public')->exists($signature->signature_image)) {
            Storage::disk('public')->delete($signature->signature_image);
        }

        $signature->delete();

        // ถ้าเป็นลายเซ็นผู้รายงาน → กลับเป็นสถานะ draft
        if ($signature->role === 'reporter') {
            $report->update(['status' => 'draft']);
        }

        return redirect()->route('reports.show', $report)
            ->with('success', 'ลบลายเซ็นเรียบร้อยแล้ว');
    }

    // ========== Helper: บันทึก base64 → file ==========
    private function saveDrawnSignature($base64Data, $reportId, $userId)
    {
        // แยก header กับ data (data:image/png;base64,iVBORw0KG...)
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            $extension = $matches[1];
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        } else {
            $extension = 'png';
        }

        // Decode
        $imageData = base64_decode($base64Data);

        // ตั้งชื่อไฟล์
        $filename = 'signature_reporter_' . $reportId . '_' . time() . '.' . $extension;
        $path = 'signatures/' . $filename;

        // บันทึก
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }
}