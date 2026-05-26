@extends('layouts.app')

@section('title', 'หน้าหลัก - Dashboard')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Welcome Banner --}}
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #EF6C00 0%, #FFB74D 100%); color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-1">
                        <i class="bi bi-hand-thumbs-up-fill"></i>
                        ยินดีต้อนรับ, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}!
                    </h3>
                    <p class="mb-0" style="opacity: 0.9;">เข้าสู่ระบบสำเร็จ — ขอบคุณที่ใช้บริการของเรา</p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="bi bi-shield-check" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- 2 การ์ดข้างกัน --}}
    <div class="row g-3">

        {{-- การ์ดซ้าย: ข้อมูลบุคลากร --}}
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom" style="color: #EF6C00;">
                    <h5 class="mb-0"><i class="bi bi-person-badge-fill"></i> ข้อมูลบุคลากร</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        {{-- <tr>
                            <td style="width: 180px;"><strong>รหัสบุคลากร:</strong></td>
                            <td>#{{ str_pad(Auth::user()->id, 5, '0', STR_PAD_LEFT) }}</td>
                        </tr> --}}
                        <tr>
                            <td><strong>ชื่อ-นามสกุล:</strong></td>
                            <td>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>อีเมล:</strong></td>
                            <td>{{ Auth::user()->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>ตำแหน่งงาน:</strong></td>
                            <td>{{ Auth::user()->position }}</td>
                        </tr>
                        <tr>
                            <td><strong>สังกัดสำนัก/กอง:</strong></td>
                            <td>{{ Auth::user()->department }}</td>
                        </tr>
                        <tr>
                            <td><strong>สังกัดคณะ:</strong></td>
                            <td>{{ Auth::user()->faculty }}</td>
                        </tr>
                        <tr>
                            <td><strong>สมัครเมื่อ:</strong></td>
                            <td>{{ Auth::user()->created_at->format('d/m/Y ') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- การ์ดขวา: สถานะระบบ --}}
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom" style="color: #1e3a8a;">
                    <h5 class="mb-0"><i class="bi bi-info-circle-fill"></i> สถานะระบบ</h5>
                </div>
                <div class="card-body">
                    <p><i class="bi bi-check-circle-fill text-success"></i> ระบบล็อกอิน: ใช้งานได้</p>
                    <p><i class="bi bi-check-circle-fill text-success"></i> ฐานข้อมูล: เชื่อมต่อแล้ว</p>
                    <p><i class="bi bi-check-circle-fill text-success"></i> Session: ปลอดภัย</p>
                    <p class="mb-0">
                        <i class="bi bi-clock-fill text-primary"></i>
                        เข้าสู่ระบบเมื่อ: {{ now()->format('d/m/Y ') }}
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection