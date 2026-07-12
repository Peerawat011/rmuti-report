<?php

namespace App\Http\Controllers;

use App\Services\FileStore;
use Illuminate\Support\Facades\Storage;

/**
 * เสิร์ฟรูปสาธารณะ (avatar, รูปประกาศ) จาก database
 *
 * URL เดิม /storage/... ใช้ได้ต่อ: ถ้าไฟล์จริงไม่อยู่บนดิสก์ (Render restart แล้วไฟล์หาย)
 * nginx จะส่ง request เข้า Laravel แล้ว route นี้ดึง binary จาก stored_files แทน
 */
class FileController extends Controller
{
    // อนุญาตเฉพาะรูปสาธารณะ — ลายเซ็น (signatures/) ต้องผ่าน route ที่ตรวจสิทธิ์เท่านั้น
    private const PUBLIC_PREFIXES = ['avatars/', 'announcements/'];

    public function show(string $path)
    {
        $allowed = !str_contains($path, '..')
            && collect(self::PUBLIC_PREFIXES)->contains(fn ($p) => str_starts_with($path, $p));

        if (!$allowed) {
            abort(404);
        }

        // 1) จาก database (ระบบใหม่)
        $file = FileStore::find($path);
        if ($file) {
            return response($file->data, 200, [
                'Content-Type'  => $file->mime ?: 'application/octet-stream',
                // ชื่อไฟล์มี timestamp/uniqid — เปลี่ยนรูปแล้วชื่อเปลี่ยน cache ยาวได้
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        // 2) fallback: ไฟล์เก่าบนดิสก์ (เครื่อง dev)
        $real = Storage::disk('public')->path($path);
        if (is_file($real)) {
            return response()->file($real, ['Cache-Control' => 'public, max-age=86400']);
        }

        abort(404);
    }
}
