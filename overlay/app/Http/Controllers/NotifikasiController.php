<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\PerPage;

class NotifikasiController extends Controller {
    use PerPage;

    public function index(Request $r) {
        $notif = $r->user()->notifications()->paginate($this->perPage(20))->withQueryString();
        return view('notifikasi.index', compact('notif'));
    }

    public function markRead(Request $r, string $id) {
        $n = $r->user()->notifications()->findOrFail($id);
        $n->markAsRead();
        return back();
    }

    public function markAll(Request $r) {
        $r->user()->unreadNotifications->markAsRead();
        return back();
    }

    /**
     * Buka detail notifikasi → mark read + redirect ke target sesuai role user.
     * Url-nya bisa dari $data['url'] (literal) atau di-derive dari kelas_id/tagihan_id berdasar role.
     */
    public function open(Request $r, string $id) {
        $user = $r->user();
        $n = $user->notifications()->findOrFail($id);
        $n->markAsRead();

        $data = $n->data ?? [];
        $url = $data['url'] ?? null;

        // Kalau gak ada url eksplisit, derive berdasarkan tipe + role
        if (!$url) {
            $role = $user->role;

            // Tipe jadwal → arahkan ke list kelas saya (siswa) / kelas saya (guru) / kelas detail (admin)
            if (($data['tipe'] ?? null) === 'jadwal' && !empty($data['kelas_id'])) {
                $kelasId = (int) $data['kelas_id'];
                $url = match ($role) {
                    'siswa' => route('siswa.kelas.index'),
                    'guru'  => route('guru.kelas.show', $kelasId),
                    default => route('kelas.show', $kelasId),
                };
            }
            // Tipe tagihan
            elseif (!empty($data['tagihan_id'])) {
                $tagihanId = (int) $data['tagihan_id'];
                $url = match ($role) {
                    'siswa' => route('siswa.tagihan.index'),
                    default => route('tagihan.show', $tagihanId),
                };
            }
        }

        // Fallback aman: kalau url tujuan ke route admin tapi user bukan admin, redirect dashboard
        if (!$url) {
            $url = route('dashboard');
        }

        return redirect($url);
    }
}
