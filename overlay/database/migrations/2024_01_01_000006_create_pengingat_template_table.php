<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengingat_template', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('judul');
            $table->text('body');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pengingat_template'); }
};
