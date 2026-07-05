<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // เวลาที่ผู้บังคับบัญชาลงนามล่าสุด (ใช้แจ้งเตือนเจ้าของรายงาน)
            $table->timestamp('last_signed_at')->nullable()->after('revision_at');
            // เวลาที่เจ้าของเปิดดูล่าสุด — ถ้า last_signed_at ใหม่กว่า = มีการลงนามที่ยังไม่ได้ดู
            $table->timestamp('owner_seen_at')->nullable()->after('last_signed_at');
        });

        // Backfill: รายงานเดิมที่มีผู้บังคับบัญชาลงนามแล้ว — ตั้งเวลาและถือว่าเจ้าของเห็นแล้ว (ไม่เด้ง badge ย้อนหลัง)
        DB::statement("
            UPDATE reports r
            SET last_signed_at = (
                SELECT MAX(s.created_at) FROM signatures s
                WHERE s.report_id = r.id AND s.role LIKE 'supervisor%'
            )
        ");
        DB::statement('UPDATE reports SET owner_seen_at = NOW() WHERE last_signed_at IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['last_signed_at', 'owner_seen_at']);
        });
    }
};
