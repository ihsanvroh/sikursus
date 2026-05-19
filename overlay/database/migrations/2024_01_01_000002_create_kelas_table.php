<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode', 20)->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('tarif', 12, 2)->default(0);
            $table->foreignId('guru_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kelas'); }
};
