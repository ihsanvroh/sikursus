@extends('layouts.app')
@section('title','Notifikasi')
@section('content')
<div class="card card-stat"><div class="card-body">
  <div class="d-flex mb-3">
    <h6 class="mb-0 align-self-center">Daftar Notifikasi</h6>
    <form action="{{ route('notifikasi.read_all') }}" method="POST" class="ms-auto">@csrf<button class="btn btn-sm btn-outline-primary"><i class="bi bi-check-all"></i> Tandai Semua Dibaca</button></form>
  </div>
  @forelse($notif as $n)
    <div class="border rounded p-3 mb-2 {{ $n->read_at ? '' : 'bg-light' }}">
      <div class="d-flex">
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-2 mb-1">
            @if(!$n->read_at)<span class="badge bg-primary">Baru</span>@endif
            <strong>{{ $n->data['judul'] ?? 'Notifikasi' }}</strong>
          </div>
          <p class="mb-1" style="white-space:pre-wrap">{{ $n->data['body'] ?? '' }}</p>
          <small class="text-muted">{{ $n->created_at->diffForHumans() }} · {{ $n->created_at->format('d M Y H:i') }}</small>
          @if(!empty($n->data['url']))
            <div><a href="{{ $n->data['url'] }}" class="btn btn-sm btn-link p-0">Lihat detail →</a></div>
          @endif
        </div>
        @if(!$n->read_at)
          <form action="{{ route('notifikasi.read',$n->id) }}" method="POST">@csrf<button class="btn btn-sm btn-light" title="Tandai dibaca"><i class="bi bi-check"></i></button></form>
        @endif
      </div>
    </div>
  @empty
    <div class="text-center text-muted py-4"><i class="bi bi-bell-slash display-4 d-block mb-2"></i>Belum ada notifikasi</div>
  @endforelse
  {{ $notif->links() }}
</div></div>
@endsection
