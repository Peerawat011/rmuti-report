<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            // ระบบใหม่อ้างอิง user_signature_id — คอลัมน์ไฟล์ต่อรายงานเหลือไว้เพื่อข้อมูลเก่าเท่านั้น
            $table->string('signature_image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->string('signature_image')->nullable(false)->change();
        });
    }
};
