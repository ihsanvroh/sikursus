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
}
