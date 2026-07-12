<?php

namespace App\Services;

use App\Models\StoredFile;

/**
 * เก็บ/อ่านไฟล์รูปจาก database (ตาราง stored_files)
 *
 * ทำไมไม่เก็บลงดิสก์: Render free tier เป็น ephemeral filesystem —
 * ไฟล์ใน storage/ หายทุกครั้งที่ deploy หรือ instance restart
 * เก็บ binary ลง TiDB จึงอยู่ถาวรเหมือนข้อมูลอื่น
 */
class FileStore
{
    public static function put(string $path, string $binary, ?string $mime = null): void
    {
        StoredFile::updateOrCreate(
            ['path' => $path],
            ['mime' => $mime, 'data' => $binary],
        );
    }

    public static function get(string $path): ?string
    {
        $file = StoredFile::where('path', $path)->first();

        return $file?->data;
    }

    public static function find(string $path): ?StoredFile
    {
        return StoredFile::where('path', $path)->first();
    }

    public static function exists(string $path): bool
    {
        return StoredFile::where('path', $path)->exists();
    }

    public static function delete(string $path): void
    {
        StoredFile::where('path', $path)->delete();
    }
}
