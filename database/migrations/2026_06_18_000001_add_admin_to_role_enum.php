<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // เพิ่ม 'admin' เข้าไปใน enum role (เดิมมี staff, supervisor)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff','supervisor','admin') NOT NULL DEFAULT 'staff'");
    }

    public function down(): void
    {
        // เปลี่ยน admin ที่มีอยู่กลับเป็น staff ก่อน เพื่อไม่ให้ค่าตกค้างผิด enum
        DB::statement("UPDATE users SET role = 'staff' WHERE role = 'admin'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff','supervisor') NOT NULL DEFAULT 'staff'");
    }
};
