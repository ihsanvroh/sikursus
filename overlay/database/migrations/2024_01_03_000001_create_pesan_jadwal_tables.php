<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Template pesan jadwal yang dipakai guru
        Schema::create('pesan_jadwal_template', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120);
            $table->string('judul', 200);
            $table->text('body');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Setting H-N notifikasi jadwal kelas otomatis (per admin)
        Schema::create('jadwal_notif_setting', function (Blueprint $table) {
            $table->id();
            $table->integer('hari_offset')->default(1); // 0=hari H, 1=H-1, 3=H-3, dst
            $table->time('jam_kirim')->default('19:00:00'); // jam kirim notif
            $table->boolean('aktif')->default(true);
            $table->string('keterangan', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('jadwal_notif_setting');
        Schema::dropIfExists('pesan_jadwal_template');
    }
};
