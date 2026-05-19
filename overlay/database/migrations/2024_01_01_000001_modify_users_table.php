<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin','guru','siswa'])->default('siswa')->after('email');
            $table->string('nohp', 20)->nullable()->after('role');
            $table->text('alamat')->nullable()->after('nohp');
            $table->date('tgl_lahir')->nullable()->after('alamat');
            $table->enum('gender', ['L','P'])->nullable()->after('tgl_lahir');
            $table->string('foto')->nullable()->after('gender');
            $table->boolean('aktif')->default(true)->after('foto');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','nohp','alamat','tgl_lahir','gender','foto','aktif']);
        });
    }
};
