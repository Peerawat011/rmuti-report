<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();                              // รหัสประจำตัวบุคลากร
        $table->string('first_name');              // ชื่อ
        $table->string('last_name');               // นามสกุล
        $table->string('email')->unique();         // อีเมล (ไม่ซ้ำ)
        $table->string('position');                // ตำแหน่งงาน
        $table->string('department');              // สังกัดสำนัก/สถาบัน/กอง
        $table->string('faculty');                 // สังกัดคณะ
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');                // รหัสผ่าน (เข้ารหัส)
        $table->rememberToken();
        $table->timestamps();                      // created_at + updated_at
    });

    Schema::create('password_reset_tokens', function (Blueprint $table) {
        $table->string('email')->primary();
        $table->string('token');
        $table->timestamp('created_at')->nullable();
    });

    Schema::create('sessions', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
