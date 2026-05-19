<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Pengingat\TemplateController as PgTemplateController;
use App\Http\Controllers\Pengingat\SettingController as PgSettingController;
use App\Http\Controllers\Pengingat\KirimController as PgKirimController;
use App\Http\Controllers\Pengingat\LogController as PgLogController;
use App\Http\Controllers\Admin\JadwalNotifSettingController;
use App\Http\Controllers\Admin\PesanJadwalTemplateController;
use App\Http\Controllers\Guru\SiswaSayaController;
use App\Http\Controllers\Guru\KelasSayaController as GuruKelasSayaController;
use App\Http\Controllers\Guru\PesanJadwalController;
use App\Http\Controllers\Siswa\KelasSayaController as SiswaKelasSayaController;
use App\Http\Controllers\Siswa\TagihanSayaController;

Route::get('/', fn() => redirect('/dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class,'showLogin'])->name('login');
    Route::post('/login', [AuthController::class,'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Notifikasi (semua user) + redirector role-aware
    Route::get('/notifikasi', [NotifikasiController::class,'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{id}/buka', [NotifikasiController::class,'open'])->name('notifikasi.open');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class,'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class,'markAll'])->name('notifikasi.read_all');

    // ============================================================
    // GURU only — DAFTARKAN DULUAN supaya path /guru/kelas-saya
    // gak ke-match Route::resource('guru') admin yg pakai param {guru}.
    // ============================================================
    Route::middleware('role:guru')->group(function () {
        Route::get('guru/siswa-saya', [SiswaSayaController::class,'index'])->name('guru.siswa.index');
        Route::get('guru/kelas-saya', [GuruKelasSayaController::class,'index'])->name('guru.kelas.index');
        Route::get('guru/kelas-saya/{kela}', [GuruKelasSayaController::class,'show'])->name('guru.kelas.show')->whereNumber('kela')->scopeBindings();
        Route::get('guru/pesan-jadwal', [PesanJadwalController::class,'form'])->name('guru.pesan.form');
        Route::post('guru/pesan-jadwal', [PesanJadwalController::class,'send'])->name('guru.pesan.send');
        Route::get('guru/pesan-jadwal/siswa', [PesanJadwalController::class,'ajaxSiswa'])->name('guru.pesan.ajax_siswa');
    });

    // ============================================================
    // SISWA only — daftar duluan juga supaya gak bentrok dgn
    // Route::resource('siswa') admin yg pakai model binding {siswa}.
    // ============================================================
    Route::middleware('role:siswa')->group(function () {
        Route::get('siswa/kelas-saya', [SiswaKelasSayaController::class,'index'])->name('siswa.kelas.index');
        Route::get('siswa/tagihan-saya', [TagihanSayaController::class,'index'])->name('siswa.tagihan.index');
        Route::get('siswa/tagihan-saya/{tagihan}/pdf', [TagihanSayaController::class,'downloadPdf'])->name('siswa.tagihan.pdf')->whereNumber('tagihan');
    });

    // ============================================================
    // ADMIN only — resource routes pakai whereNumber buat param numeric
    // biar gak nyamber path string.
    // ============================================================
    Route::middleware('role:admin')->group(function () {
        Route::resource('siswa', SiswaController::class)->whereNumber('siswa');
        Route::post('siswa/{siswa}/tambah-kelas', [SiswaController::class,'tambahKelas'])->name('siswa.tambah_kelas')->whereNumber('siswa');
        Route::post('enrollment/{enrollment}/pindah', [SiswaController::class,'gantiKelas'])->name('siswa.ganti_kelas')->whereNumber('enrollment');
        Route::post('enrollment/{enrollment}/selesai', [SiswaController::class,'selesaikanEnrollment'])->name('siswa.enrollment_selesai')->whereNumber('enrollment');

        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kela'])->whereNumber('kela');
        Route::resource('guru', GuruController::class)->whereNumber('guru');

        Route::get('tagihan', [TagihanController::class,'index'])->name('tagihan.index');
        Route::get('tagihan/create', [TagihanController::class,'create'])->name('tagihan.create');
        Route::get('tagihan/export/pdf', [TagihanController::class,'exportPdf'])->name('tagihan.export.pdf');
        Route::get('tagihan/export/excel', [TagihanController::class,'exportExcel'])->name('tagihan.export.excel');
        Route::post('tagihan', [TagihanController::class,'store'])->name('tagihan.store');
        Route::post('tagihan/generate', [TagihanController::class,'generate'])->name('tagihan.generate');
        Route::get('tagihan/{tagihan}', [TagihanController::class,'show'])->name('tagihan.show')->whereNumber('tagihan');
        Route::get('tagihan/{tagihan}/pdf', [TagihanController::class,'downloadPdf'])->name('tagihan.pdf')->whereNumber('tagihan');
        Route::delete('tagihan/{tagihan}', [TagihanController::class,'destroy'])->name('tagihan.destroy')->whereNumber('tagihan');

        Route::post('tagihan/{tagihan}/pembayaran', [PembayaranController::class,'store'])->name('pembayaran.store')->whereNumber('tagihan');
        Route::delete('pembayaran/{pembayaran}', [PembayaranController::class,'destroy'])->name('pembayaran.destroy')->whereNumber('pembayaran');

        Route::get('laporan', [LaporanController::class,'index'])->name('laporan.index');
        Route::get('laporan/export/pdf', [LaporanController::class,'exportPdf'])->name('laporan.export.pdf');
        Route::get('laporan/export/excel', [LaporanController::class,'exportExcel'])->name('laporan.export.excel');

        // Pengingat
        Route::resource('pengingat/template', PgTemplateController::class)->names('pengingat.template')->parameters(['template' => 'template'])->whereNumber('template');
        Route::get('pengingat/setting', [PgSettingController::class,'index'])->name('pengingat.setting.index');
        Route::post('pengingat/setting', [PgSettingController::class,'store'])->name('pengingat.setting.store');
        Route::put('pengingat/setting/{setting}', [PgSettingController::class,'update'])->name('pengingat.setting.update')->whereNumber('setting');
        Route::delete('pengingat/setting/{setting}', [PgSettingController::class,'destroy'])->name('pengingat.setting.destroy')->whereNumber('setting');

        Route::get('pengingat/kirim', [PgKirimController::class,'form'])->name('pengingat.kirim.form');
        Route::post('pengingat/kirim', [PgKirimController::class,'send'])->name('pengingat.kirim.send');
        Route::get('pengingat/kirim/tagihan', [PgKirimController::class,'ajaxTagihan'])->name('pengingat.kirim.ajax_tagihan');

        Route::get('pengingat/log', [PgLogController::class,'index'])->name('pengingat.log.index');

        // Notifikasi Jadwal (admin)
        Route::resource('admin/pesan-template', PesanJadwalTemplateController::class)
            ->names('admin.pesan_template')
            ->parameters(['pesan-template' => 'template'])
            ->whereNumber('template');

        Route::get('admin/jadwal-notif', [JadwalNotifSettingController::class,'index'])->name('admin.jadwal_notif.index');
        Route::post('admin/jadwal-notif', [JadwalNotifSettingController::class,'store'])->name('admin.jadwal_notif.store');
        Route::put('admin/jadwal-notif/{setting}', [JadwalNotifSettingController::class,'update'])->name('admin.jadwal_notif.update')->whereNumber('setting');
        Route::delete('admin/jadwal-notif/{setting}', [JadwalNotifSettingController::class,'destroy'])->name('admin.jadwal_notif.destroy')->whereNumber('setting');
    });
});
