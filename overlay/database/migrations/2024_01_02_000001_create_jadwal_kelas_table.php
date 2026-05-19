<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->unsignedTinyInteger('hari'); // 1=Senin .. 7=Minggu
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['kelas_id','hari']);
            $table->index(['hari','jam_mulai','jam_selesai']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('jadwal_kelas');
    }
};
