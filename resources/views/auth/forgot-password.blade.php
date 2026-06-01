@extends('layouts.auth')

@section('content')
<div class="card p-4 shadow-sm">
    <h3 class="text-center mb-3">Lupa Password?</h3>
    <p class="text-center text-muted small">Masukkan email yang terdaftar, kami akan mengirimkan tautan untuk mengatur ulang password Anda.</p>
    
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-warning w-100">Kirim Link Reset</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-muted small mb-0" style="color: #0b1b35;">
            <i class="text-warning fw-bold text-decoration-none"></i> Kembali ke Login
        </a>
    </div>
</div>
@endsection