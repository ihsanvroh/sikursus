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

Route::get('/', fn() => redirect('/dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class,'showLogin'])->name('login');
    Route::post('/login', [AuthController::class,'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Notifikasi (semua user)
    Route::get('/notifikasi', [NotifikasiController::class,'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class,'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class,'markAll'])->name('notifikasi.read_all');

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('siswa', SiswaController::class);
        Route::post('siswa/{siswa}/tambah-kelas', [SiswaController::class,'tambahKelas'])->name('siswa.tambah_kelas');
        Route::post('enrollment/{enrollment}/pindah', [SiswaController::class,'gantiKelas'])->name('siswa.ganti_kelas');
        Route::post('enrollment/{enrollment}/selesai', [SiswaController::class,'selesaikanEnrollment'])->name('siswa.enrollment_selesai');

        Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kela']);
        Route::resource('guru', GuruController::class);

        Route::get('tagihan', [TagihanController::class,'index'])->name('tagihan.index');
        Route::get('tagihan/create', [TagihanController::class,'create'])->name('tagihan.create');
        Route::get('tagihan/export/pdf', [TagihanController::class,'exportPdf'])->name('tagihan.export.pdf');
        Route::get('tagihan/export/excel', [TagihanController::class,'exportExcel'])->name('tagihan.export.excel');
        Route::post('tagihan', [TagihanController::class,'store'])->name('tagihan.store');
        Route::post('tagihan/generate', [TagihanController::class,'generate'])->name('tagihan.generate');
        Route::get('tagihan/{tagihan}', [TagihanController::class,'show'])->name('tagihan.show');
        Route::get('tagihan/{tagihan}/pdf', [TagihanController::class,'downloadPdf'])->name('tagihan.pdf');
        Route::delete('tagihan/{tagihan}', [TagihanController::class,'destroy'])->name('tagihan.destroy');

        Route::post('tagihan/{tagihan}/pembayaran', [PembayaranController::class,'store'])->name('pembayaran.store');
        Route::delete('pembayaran/{pembayaran}', [PembayaranController::class,'destroy'])->name('pembayaran.destroy');

        Route::get('laporan', [LaporanController::class,'index'])->name('laporan.index');
        Route::get('laporan/export/pdf', [LaporanController::class,'exportPdf'])->name('laporan.export.pdf');
        Route::get('laporan/export/excel', [LaporanController::class,'exportExcel'])->name('laporan.export.excel');

        // Pengingat
        Route::resource('pengingat/template', PgTemplateController::class)->names('pengingat.template')->parameters(['template' => 'template']);
        Route::get('pengingat/setting', [PgSettingController::class,'index'])->name('pengingat.setting.index');
        Route::post('pengingat/setting', [PgSettingController::class,'store'])->name('pengingat.setting.store');
        Route::put('pengingat/setting/{setting}', [PgSettingController::class,'update'])->name('pengingat.setting.update');
        Route::delete('pengingat/setting/{setting}', [PgSettingController::class,'destroy'])->name('pengingat.setting.destroy');

        Route::get('pengingat/kirim', [PgKirimController::class,'form'])->name('pengingat.kirim.form');
        Route::post('pengingat/kirim', [PgKirimController::class,'send'])->name('pengingat.kirim.send');
        Route::get('pengingat/kirim/tagihan', [PgKirimController::class,'ajaxTagihan'])->name('pengingat.kirim.ajax_tagihan');

        Route::get('pengingat/log', [PgLogController::class,'index'])->name('pengingat.log.index');
    });
});
