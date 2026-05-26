<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // ส่วนที่ 1 — ข้อมูลบุคลากร (snapshot ตอนสร้าง — กันข้อมูลเปลี่ยนภายหลัง)
            $table->string('reporter_name');
            $table->string('reporter_position');
            $table->string('reporter_department');
            $table->string('reporter_faculty');

            // ส่วนที่ 2 — รายละเอียดการเข้ารับ
            $table->enum('activity_type', ['อบรม', 'ศึกษาดูงาน', 'ประชุมสัมมนา']);
            $table->string('topic');                          // 2.1 หัวข้อเรื่อง
            $table->string('order_number')->nullable();       // 2.2 เลขที่คำสั่ง
            $table->date('start_date');                       // ระหว่างวันที่
            $table->date('end_date');                         // ถึงวันที่
            $table->integer('total_days');                    // เป็นเวลารวม
            $table->text('location');                         // 2.3 สถานที่
            $table->string('organizer');                      // 2.4 หน่วยงานดำเนินการ

            // 2.5 การใช้ประโยชน์ (checkbox หลายตัว — เก็บเป็น JSON)
            $table->json('usage_types')->nullable();          // ['ปฏิบัติงาน', 'ขยายผล']
            $table->string('usage_other')->nullable();        // อื่นๆ ระบุ

            // ส่วนที่ 3-5 — เนื้อหา
            $table->text('documents')->nullable();            // 3. เอกสาร/ตำรา/คู่มือ
            $table->text('details');                          // 4. รายละเอียด
            $table->text('suggestions');                      // 5. ข้อคิดเห็น/ข้อเสนอแนะ

            // สถานะของรายงาน
            $table->enum('status', ['draft', 'pending_signature', 'signed', 'approved'])
                  ->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};