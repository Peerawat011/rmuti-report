<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * เก็บตัวไฟล์รูป (binary) ลง database แทนดิสก์
 * — Render free tier เป็น ephemeral filesystem ไฟล์หายทุกครั้งที่ deploy/restart
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stored_files', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique();      // path เดิมที่เก็บใน column ต่างๆ เช่น avatars/xxx.png
            $table->string('mime', 100)->nullable();
            $table->binary('data');
            $table->timestamps();
        });

        // Laravel สร้าง binary เป็น BLOB (64KB) — ขยายเป็น MEDIUMBLOB (16MB) รองรับรูปใหญ่สุด 4MB
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE stored_files MODIFY data MEDIUMBLOB NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stored_files');
    }
};
