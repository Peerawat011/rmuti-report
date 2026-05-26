@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
    <img src="{{ asset('https://ess-register.rmuti.ac.th/AppKK/assets/media/logos/demo7.svg') }}"
         alt="RMUTI Logo"
         style="width: 70px; height: 70px;  padding: 8px; border-radius: 50%; margin-bottom: 8px;">
    <h4 class="mt-2 mb-0">เข้าสู่ระบบ</h4>
    <small style="opacity: 0.9;">Login to your account</small>
</div>

        <div class="auth-body">

            {{-- แสดง Success Message --}}
            @if(session('success'))
                <div class="alert alert-success py-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            {{-- แสดง Error Message --}}
            @if($errors->any())
                <div class="alert alert-danger py-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope-fill text-primary"></i> อีเมล
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-at"></i></span>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="example@email.com"
                               required autofocus>
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock-fill text-primary"></i> รหัสผ่าน
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password"
                               class="form-control"
                               id="password" name="password"
                               placeholder="กรอกรหัสผ่านของคุณ"
                               required>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">จดจำฉัน</label>
                    </div>
                    <a href="#" class="link-gov">ลืมรหัสผ่าน?</a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-gov w-100">
                    <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
                </button>
            </form>

            <hr class="my-3">

            <div class="text-center">
                <small class="text-muted">ยังไม่มีบัญชี?</small>
                <a href="{{ route('register') }}" class="link-gov">ลงทะเบียนที่นี่</a>
            </div>

        </div>
    </div>
</div>
@endsection