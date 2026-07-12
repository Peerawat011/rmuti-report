<?php

namespace App\Http\Controllers;

use App\Models\Signature;
use App\Models\UserSignature;
use App\Services\SignatureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSignatureController extends Controller
{
    // ========== เสิร์ฟรูปลายเซ็นของรายงาน (ตรวจสิทธิ์ + watermark) ==========
    public function reportImage(Signature $signature)
    {
        $report = $signature->report;
        $user = Auth::user();

        // สิทธิ์: admin / เจ้าของรายงาน / ผู้บังคับบัญชาของรายงานนี้
        $allowed = $user->isAdmin()
            || $report->user_id === $user->id
            || in_array($user->id, array_filter([
                $report->supervisor_1_id,
                $report->supervisor_2_id,
                $report->supervisor_3_id,
            ]));

        if (!$allowed) {
            abort(403, 'คุณไม่มีสิทธิ์ดูลายเซ็นนี้');
        }

        $png = SignatureImage::render($signature);

        if (!$png) {
            abort(404);
        }

        return response($png, 200, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    // ========== พรีวิวลายเซ็นประจำตัวของตัวเอง (ในหน้าโปรไฟล์) ==========
    public function myPreview()
    {
        $sig = Auth::user()->activeSignature;

        $png = $sig ? SignatureImage::read($sig->path) : null;
        if (!$png) {
            abort(404);
        }

        return response($png, 200, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    // ========== บันทึกลายเซ็นประจำตัวเวอร์ชันใหม่ (จากหน้าโปรไฟล์) ==========
    public function store(Request $request)
    {
        $request->validate([
            'signature_mode' => 'required|in:draw,upload',
            'signature_data' => 'required_if:signature_mode,draw|nullable|string',
            'signature_file' => 'required_if:signature_mode,upload|nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'signature_data.required_if' => 'กรุณาวาดลายเซ็น',
            'signature_file.required_if' => 'กรุณาเลือกรูปลายเซ็น',
            'signature_file.image'       => 'ไฟล์ต้องเป็นรูปภาพ',
            'signature_file.mimes'       => 'รองรับเฉพาะ JPG, PNG',
            'signature_file.max'         => 'ขนาดไฟล์ต้องไม่เกิน 2 MB',
        ]);

        self::createVersion($request, Auth::id());

        return back()->with('success', 'บันทึกลายเซ็นประจำตัวเรียบร้อยแล้ว — รายงานที่ลงนามไว้ก่อนหน้ายังใช้ลายเซ็นเวอร์ชันเดิม');
    }

    /**
     * Helper กลาง: สร้างเวอร์ชันลายเซ็นประจำตัวจาก request (draw base64 / upload)
     * ใช้ร่วมกันทั้งหน้าโปรไฟล์และหน้าลงนาม
     */
    public static function createVersion(Request $request, int $userId): UserSignature
    {
        if ($request->signature_mode === 'draw') {
            $data = $request->signature_data;
            if (preg_match('/^data:image\/\w+;base64,/', $data)) {
                $data = substr($data, strpos($data, ',') + 1);
            }
            $binary = base64_decode($data);
        } else {
            $binary = file_get_contents($request->file('signature_file')->getRealPath());
        }

        $path = SignatureImage::store($binary, $userId);

        return UserSignature::create([
            'user_id' => $userId,
            'path'    => $path,
        ]);
    }
}
