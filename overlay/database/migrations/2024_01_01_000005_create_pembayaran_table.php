<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->decimal('nominal', 12, 2);
            $table->enum('metode', ['tunai','transfer','qris','lainnya'])->default('tunai');
            $table->string('referensi')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
            $table->index('tanggal_bayar');
        });
    }
    public function down(): void { Schema::dropIfExists('pembayaran'); }
};
