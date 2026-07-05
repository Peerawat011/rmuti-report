@extends('layouts.app')

@section('title', 'ตั้งรหัสผ่านใหม่')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <i class="bi bi-shield-lock-fill" style="font-size: 2rem;"></i>
            <h4 class="mt-2 mb-0">ตั้งรหัสผ่านใหม่</h4>
            <small style="opacity: 0.9;">Set a new password</small>
        </div>

        <div class="auth-body">

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>พบข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-1" style="padding-left: 1.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- อีเมล (ล็อกจากลิงก์) --}}
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope-fill text-primary"></i> อีเมล
                    </label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email', $email) }}"
                           {{ $email ? 'readonly' : 'required' }}>
                </div>

                {{-- รหัสผ่านใหม่ --}}
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock-fill text-primary"></i> รหัสผ่านใหม่ <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="อย่างน้อย 6 ตัวอักษร" required autofocus>
                </div>

                {{-- ยืนยันรหัสผ่านใหม่ --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="bi bi-shield-check text-primary"></i> ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                           placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" required>
                </div>

                <button type="submit" class="btn btn-gov w-100">
                    <i class="bi bi-check2-circle"></i> บันทึกรหัสผ่านใหม่
                </button>
            </form>

            <hr class="my-3">

            <div class="text-center">
                <a href="{{ route('login') }}" class="link-gov">
                    <i class="bi bi-arrow-left"></i> กลับไปหน้าเข้าสู่ระบบ
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
