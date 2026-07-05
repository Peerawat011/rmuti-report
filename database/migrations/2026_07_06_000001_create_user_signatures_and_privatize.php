<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        // ลายเซ็นประจำตัวผู้ใช้ (มีหลายเวอร์ชัน — เอกสารเก่าอ้างเวอร์ชันเดิมเสมอ)
        Schema::create('user_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('path');                    // path ใน private disk
            $table->timestamps();
            $table->index(['user_id', 'id']);
        });

        // ลายเซ็นในรายงาน อ้างอิงเวอร์ชันลายเซ็นประจำตัว (nullable — รองรับข้อมูลเก่าที่เป็นไฟล์ต่อรายงาน)
        Schema::table('signatures', function (Blueprint $table) {
            $table->foreignId('user_signature_id')->nullable()->after('signature_image')
                  ->constrained('user_signatures')->nullOnDelete();
        });

        // ย้ายไฟล์ลายเซ็นเดิมจาก public → private (ปิดการเข้าถึงตรงผ่าน /storage)
        $publicDisk  = Storage::disk('public');
        $privateDisk = Storage::disk('local');

        if ($publicDisk->exists('signatures')) {
            foreach ($publicDisk->files('signatures') as $file) {
                if (!$privateDisk->exists($file)) {
                    $privateDisk->put($file, $publicDisk->get($file));
                }
                $publicDisk->delete($file);
            }
        }
    }

    public function down(): void
    {
        // ย้ายไฟล์กลับ public
        $publicDisk  = Storage::disk('public');
        $privateDisk = Storage::disk('local');

        if ($privateDisk->exists('signatures')) {
            foreach ($privateDisk->files('signatures') as $file) {
                if (!$publicDisk->exists($file)) {
                    $publicDisk->put($file, $privateDisk->get($file));
                }
                $privateDisk->delete($file);
            }
        }

        Schema::table('signatures', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_signature_id');
        });

        Schema::dropIfExists('user_signatures');
    }
};
