<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // เก็บ ID ของ supervisor ที่ผู้รายงานเลือก
            $table->foreignId('supervisor_1_id')->nullable()->after('user_id')->constrained('users');
            $table->foreignId('supervisor_2_id')->nullable()->after('supervisor_1_id')->constrained('users');
            $table->foreignId('supervisor_3_id')->nullable()->after('supervisor_2_id')->constrained('users');

            // เปลี่ยน enum status เพิ่ม value ใหม่
            // ไม่ต้องแก้ตรงนี้ — เราจะใช้ value 'pending_signature' ที่มีอยู่แล้ว
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['supervisor_1_id', 'supervisor_2_id', 'supervisor_3_id']);
            $table->dropColumn(['supervisor_1_id', 'supervisor_2_id', 'supervisor_3_id']);
        });
    }
};