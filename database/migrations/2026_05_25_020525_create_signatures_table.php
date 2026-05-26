<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');

            // บทบาทผู้เซ็น
            $table->enum('role', ['reporter', 'supervisor_1', 'supervisor_2', 'supervisor_3']);

            // ข้อมูลผู้เซ็น
            $table->string('signer_name');
            $table->string('signer_position');
            $table->text('comment')->nullable();              // ความเห็น (เฉพาะ supervisor)
            $table->string('signature_image');                // path รูปลายเซ็น
            $table->date('signed_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};