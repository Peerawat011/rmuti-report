<?php

namespace App\Services;

use App\Models\Signature;
use Illuminate\Support\Facades\Storage;

/**
 * จัดการรูปลายเซ็นแบบปลอดภัย:
 * - บันทึก: ย่อขนาด + บีบอัด → เก็บใน private disk (ไม่มี URL สาธารณะ)
 * - แสดงผล: ประทับ watermark เลขที่เอกสาร กันนำรูปไปใช้กับเอกสารอื่น
 */
class SignatureImage
{
    private const MAX_WIDTH  = 500;
    private const MAX_HEIGHT = 250;

    /**
     * แปลงรูป (binary) → ย่อ/บีบอัด → บันทึกลง private disk
     * คืนค่า path สำหรับเก็บใน database
     */
    public static function store(string $binary, int $userId): string
    {
        $src = @imagecreatefromstring($binary);
        if ($src === false) {
            abort(422, 'ไฟล์รูปลายเซ็นไม่ถูกต้อง');
        }

        $w = imagesx($src);
        $h = imagesy($src);

        // ย่อถ้าใหญ่เกิน (คงสัดส่วน)
        $scale = min(self::MAX_WIDTH / $w, self::MAX_HEIGHT / $h, 1);
        $newW = (int) round($w * $scale);
        $newH = (int) round($h * $scale);

        $dst = imagecreatetruecolor($newW, $newH);
        // คงพื้นหลังโปร่งใส (ลายเซ็นที่วาดมาเป็น PNG โปร่งใส)
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        ob_start();
        imagepng($dst, null, 9);   // บีบอัดสูงสุด
        $png = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        $path = 'signatures/users/' . $userId . '/sig_' . now()->format('YmdHis') . '_' . substr(md5($png), 0, 8) . '.png';
        // เก็บลง database — ดิสก์บน Render เป็น ephemeral ไฟล์หายตอน restart
        FileStore::put($path, $png, 'image/png');

        return $path;
    }

    /**
     * เรนเดอร์รูปลายเซ็นของรายงานพร้อม watermark เลขที่เอกสาร
     * คืนค่า PNG binary (หรือ null ถ้าไฟล์หาย)
     */
    public static function render(Signature $signature): ?string
    {
        $binary = self::binary($signature);
        if (!$binary) {
            return null;
        }

        $img = @imagecreatefromstring($binary);
        if ($img === false) {
            return null;
        }

        imagealphablending($img, true);
        imagesavealpha($img, true);

        // ประทับเลขที่เอกสาร (จางๆ มุมล่าง) — กันนำรูปไปใช้กับเอกสารอื่น
        $report = $signature->report;
        $label = 'ใช้กับเอกสาร ' . ($report->doc_number ?? ('#' . str_pad($report->id, 4, '0', STR_PAD_LEFT))) . ' เท่านั้น';
        $font  = storage_path('fonts/THSarabunNew.ttf');

        if (is_file($font)) {
            $gray = imagecolorallocatealpha($img, 120, 120, 120, 55); // เทาโปร่งแสง
            $size = max(10, (int) (imagesx($img) / 28));
            imagettftext($img, $size, 0, 6, imagesy($img) - 6, $gray, $font, $label);
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return $png;
    }

    /**
     * ดึง binary ของรูปลายเซ็น (รองรับทั้งระบบใหม่และไฟล์เก่า)
     * ลำดับ: database → ไฟล์บนดิสก์ (ข้อมูลเก่าบนเครื่อง dev)
     */
    public static function binary(Signature $signature): ?string
    {
        // ระบบใหม่: อ้างอิงลายเซ็นประจำตัว
        if ($signature->user_signature_id && $signature->userSignature) {
            $bin = self::read($signature->userSignature->path);
            if ($bin !== null) {
                return $bin;
            }
        }

        // ไฟล์ต่อรายงาน (ข้อมูลเก่า)
        if ($signature->signature_image) {
            return self::read($signature->signature_image);
        }

        return null;
    }

    /**
     * อ่านไฟล์ตาม path: database ก่อน แล้วค่อยดิสก์ (local → public เผื่อไฟล์เก่ายังไม่ migrate)
     */
    public static function read(string $path): ?string
    {
        $bin = FileStore::get($path);
        if ($bin !== null) {
            return $bin;
        }

        foreach (['local', 'public'] as $disk) {
            $p = Storage::disk($disk)->path($path);
            if (is_file($p)) {
                return file_get_contents($p);
            }
        }

        return null;
    }
}
