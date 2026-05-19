<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai')->nullable();
            $table->enum('status', ['aktif','selesai','berhenti'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index(['siswa_id','status']);
            $table->index(['kelas_id','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('enrollments'); }
};
