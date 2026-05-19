<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->string('periode', 7); // YYYY-MM
            $table->decimal('nominal', 12, 2);
            $table->decimal('terbayar', 12, 2)->default(0);
            $table->date('jatuh_tempo');
            $table->enum('status', ['belum_bayar','sebagian','lunas'])->default('belum_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id','kelas_id','periode'], 'tagihan_unique_periode');
            $table->index('status');
            $table->index('periode');
        });
    }
    public function down(): void { Schema::dropIfExists('tagihan'); }
};
