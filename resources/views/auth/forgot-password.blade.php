@extends('layouts.app')

@section('title', 'ลืมรหัสผ่าน')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <i class="bi bi-key-fill" style="font-size: 2rem;"></i>
            <h4 class="mt-2 mb-0">ลืมรหัสผ่าน</h4>
            <small style="opacity: 0.9;">Reset your password</small>
        </div>

        <div class="auth-body">

            @if(session('success'))
                <div class="alert alert-success py-2">
                    <i class="bi bi-envelope-check-fill"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <p class="text-muted small">
                กรอกอีเมลที่ใช้สมัครสมาชิก ระบบจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ไปให้ทางอีเมล
                (ลิงก์มีอายุ 60 นาที)
            </p>

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope-fill text-primary"></i> อีเมลที่ใช้สมัคร <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-at"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="example@rmuti.ac.th" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn btn-gov w-100">
                    <i class="bi bi-send-fill"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน
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
