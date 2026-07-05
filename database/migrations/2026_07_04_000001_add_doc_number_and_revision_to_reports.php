<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ตัวนับเลขที่เอกสาร — รันต่อปี แยกตามประเภทฟอร์ม (รองรับหลายฟอร์มในอนาคต)
        Schema::create('document_counters', function (Blueprint $table) {
            $table->id();
            $table->string('form_type');            // เช่น 'training' = แบบรายงานพัฒนาบุคลากร
            $table->unsignedSmallInteger('year');   // ปี พ.ศ.
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
            $table->unique(['form_type', 'year']);
        });

        Schema::table('reports', function (Blueprint $table) {
            // เลขที่เอกสารอัตโนมัติ เช่น "พบ. 0001/2569"
            $table->string('doc_number')->nullable()->unique()->after('user_id');

            // ข้อมูลการตีกลับให้แก้ไข
            $table->text('revision_reason')->nullable()->after('status');
            $table->foreignId('revision_by')->nullable()->after('revision_reason')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('revision_at')->nullable()->after('revision_by');
        });

        // เพิ่มสถานะ 'revision' (ส่งกลับแก้ไข) เข้า enum
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('draft','pending_signature','signed','approved','revision') NOT NULL DEFAULT 'draft'");

        // Backfill: ออกเลขที่ให้รายงานเดิมตามลำดับที่สร้าง (แยกปี พ.ศ.)
        $reports = DB::table('reports')->orderBy('created_at')->orderBy('id')->get(['id', 'created_at']);
        $counters = [];

        foreach ($reports as $report) {
            $year = (int) date('Y', strtotime($report->created_at)) + 543;
            $counters[$year] = ($counters[$year] ?? 0) + 1;

            DB::table('reports')->where('id', $report->id)->update([
                'doc_number' => sprintf('พบ. %04d/%d', $counters[$year], $year),
            ]);
        }

        foreach ($counters as $year => $last) {
            DB::table('document_counters')->insert([
                'form_type'   => 'training',
                'year'        => $year,
                'last_number' => $last,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::statement("UPDATE reports SET status = 'draft' WHERE status = 'revision'");
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('draft','pending_signature','signed','approved') NOT NULL DEFAULT 'draft'");

        Schema::table('reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('revision_by');
            $table->dropColumn(['doc_number', 'revision_reason', 'revision_at']);
        });

        Schema::dropIfExists('document_counters');
    }
};
