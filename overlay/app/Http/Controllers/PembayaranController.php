<?php
namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class PembayaranController extends Controller {
    public function store(Request $request, Tagihan $tagihan) {
        $data = $request->validate([
            'tanggal_bayar' => 'required|date',
            'nominal' => 'required|numeric|min:1',
            'metode' => 'required|in:tunai,transfer,qris,lainnya',
            'referensi' => 'nullable|string|max:120',
            'catatan' => 'nullable|string',
        ]);
        $data['admin_id'] = $request->user()->id;
        $tagihan->pembayaran()->create($data);
        return back()->with('success','Pembayaran tercatat.');
    }

    public function destroy(Pembayaran $pembayaran) {
        $pembayaran->delete();
        return back()->with('success','Pembayaran dihapus.');
    }
}
