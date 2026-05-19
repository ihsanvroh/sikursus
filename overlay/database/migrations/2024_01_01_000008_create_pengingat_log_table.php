<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengingat_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tagihan_id')->nullable()->constrained('tagihan')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('pengingat_template')->nullOnDelete();
            $table->string('channel', 20)->default('in_app');
            $table->string('mode', 20)->default('manual'); // manual | auto
            $table->string('judul');
            $table->text('body');
            $table->enum('status', ['terkirim','gagal'])->default('terkirim');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
            $table->index(['siswa_id','sent_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('pengingat_log'); }
};
