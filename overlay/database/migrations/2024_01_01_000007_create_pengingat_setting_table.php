<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengingat_setting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('pengingat_template')->cascadeOnDelete();
            // trigger: 'before' | 'after' | 'on'
            $table->enum('trigger_type', ['before','on','after']);
            $table->integer('hari_offset')->default(0); // jumlah hari sebelum/sesudah jatuh tempo
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pengingat_setting'); }
};
