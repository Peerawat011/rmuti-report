<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // ไม่บังคับกรอกรายละเอียด/ข้อเสนอแนะอีกต่อไป
            $table->text('details')->nullable()->change();
            $table->text('suggestions')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->text('details')->nullable(false)->change();
            $table->text('suggestions')->nullable(false)->change();
        });
    }
};
