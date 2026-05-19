@extends('layouts.app')
@section('title','Login')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#4338ca,#06b6d4);">
  <div class="card shadow-lg border-0" style="width:380px; border-radius:1rem;">
    <div class="card-body p-4">
      <div class="text-center mb-3">
        <div class="display-6"><i class="bi bi-mortarboard-fill text-primary"></i></div>
        <h4 class="mb-0">Si Kursus</h4>
        <small class="text-muted">Sistem Informasi Kursus</small>
      </div>
      @if($errors->any())
        <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
      @endif
      <form method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label small fw-semibold">Email</label>
          <input type="email" name="email" class="form-control" required autofocus value="{{ old('email') }}">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
          <input type="checkbox" name="remember" class="form-check-input" id="remember">
          <label class="form-check-label small" for="remember">Ingat saya</label>
        </div>
        <button class="btn btn-primary w-100">Masuk</button>
      </form>
      <div class="text-center mt-3">
        <small class="text-muted">Demo: <code>admin@sikursus.local / admin12345</code></small>
      </div>
    </div>
  </div>
</div>
@endsection
