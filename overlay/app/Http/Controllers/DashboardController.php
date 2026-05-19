<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index(Request $request) {
        $user = $request->user();
        $bulanIni = Carbon::now()->format('Y-m');

        if ($user->isAdmin()) {
            $stats = [
                'total_siswa' => User::where('role','siswa')->where('aktif',true)->count(),
                'total_guru' => User::where('role','guru')->where('aktif',true)->count(),
                'total_kelas' => Kelas::where('aktif',true)->count(),
                'tagihan_bulan_ini' => Tagihan::where('periode',$bulanIni)->count(),
                'belum_bayar' => Tagihan::where('periode',$bulanIni)->where('status','belum_bayar')->count(),
                'lunas' => Tagihan::where('periode',$bulanIni)->where('status','lunas')->count(),
                'pendapatan_bulan_ini' => Pembayaran::whereYear('tanggal_bayar', Carbon::now()->year)
                    ->whereMonth('tanggal_bayar', Carbon::now()->month)->sum('nominal'),
            ];
            $recentBayar = Pembayaran::with(['tagihan.siswa','tagihan.kelas'])->latest('tanggal_bayar')->take(5)->get();
            return view('dashboard.admin', compact('stats','recentBayar'));
        }

        if ($user->isGuru()) {
            $kelasIds = $user->kelasDiampu()->pluck('id');
            $stats = [
                'kelas_diampu' => $kelasIds->count(),
                'siswa' => \App\Models\Enrollment::whereIn('kelas_id',$kelasIds)->where('status','aktif')->distinct('siswa_id')->count('siswa_id'),
            ];
            $kelas = $user->kelasDiampu()->withCount(['siswaAktif as siswa_aktif_count'])->get();
            return view('dashboard.guru', compact('stats','kelas'));
        }

        // Siswa
        $tagihan = $user->tagihan()->with('kelas')->latest('jatuh_tempo')->take(10)->get();
        $belum = $user->tagihan()->where('status','!=','lunas')->count();
        return view('dashboard.siswa', compact('tagihan','belum'));
    }
}
