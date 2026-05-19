<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller {
    public function index(Request $r) {
        $notif = $r->user()->notifications()->paginate(20);
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
