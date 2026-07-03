<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // ผู้ประกาศ (admin)
            $table->string('title');
            $table->longText('content');
            $table->boolean('is_published')->default(true);   // เผยแพร่/ซ่อน
            $table->timestamp('published_at')->nullable();     // วันที่เผยแพร่
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
