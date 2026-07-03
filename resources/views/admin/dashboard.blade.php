@extends('layouts.app')

@section('title', 'แดชบอร์ดผู้ดูแลระบบ')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1" style="color: #EF6C00;">
                <i class="bi bi-speedometer2"></i> แดชบอร์ดผู้ดูแลระบบ
            </h3>
            <p class="text-muted mb-0">ภาพรวมข้อมูลผู้ใช้และรายงานในระบบ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-people-fill"></i> จัดการผู้ใช้
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-text-fill"></i> จัดการรายงาน
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-gov">
                <i class="bi bi-megaphone-fill"></i> จัดการประกาศ
            </a>
        </div>
    </div>

    {{-- การ์ดสรุปผู้ใช้ --}}
    <h6 class="text-muted mb-2"><i class="bi bi-people-fill"></i> ผู้ใช้งาน</h6>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people-fill" style="font-size: 2rem; color: #F57C00;"></i>
                    <h2 class="mt-2 mb-0">{{ $stats['users_total'] }}</h2>
                    <small class="text-muted">ผู้ใช้ทั้งหมด</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-person" style="font-size: 2rem; color: #6c757d;"></i>
                    <h2 class="mt-2 mb-0">{{ $stats['users_staff'] }}</h2>
                    <small class="text-muted">บุคลากรทั่วไป</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-person-badge" style="font-size: 2rem; color: #ffc107;"></i>
                    <h2 class="mt-2 mb-0">{{ $stats['users_supervisor'] }}</h2>
                    <small class="text-muted">ผู้บังคับบัญชา</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-shield-lock" style="font-size: 2rem; color: #dc3545;"></i>
                    <h2 class="mt-2 mb-0">{{ $stats['users_admin'] }}</h2>
                    <small class="text-muted">ผู้ดูแลระบบ</small>
                </div>
            </div>
        </div>
    </div>

    {{-- การ์ดสรุปรายงาน --}}
    <h6 class="text-muted mb-2"><i class="bi bi-file-earmark-text-fill"></i> รายงาน</h6>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h2 class="mb-0" style="color: #EF6C00;">{{ $stats['reports_total'] }}</h2>
                    <small class="text-muted">ทั้งหมด</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h2 class="mb-0 text-secondary">{{ $stats['reports_draft'] }}</h2>
                    <small class="text-muted">ร่าง</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h2 class="mb-0 text-warning">{{ $stats['reports_pending'] }}</h2>
                    <small class="text-muted">รอลงนาม</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h2 class="mb-0 text-info">{{ $stats['reports_signed'] }}</h2>
                    <small class="text-muted">ลงนามแล้ว</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h2 class="mb-0 text-success">{{ $stats['reports_approved'] }}</h2>
                    <small class="text-muted">อนุมัติแล้ว</small>
                </div>
            </div>
        </div>
    </div>

    {{-- รายงานล่าสุด --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0" style="color: #EF6C00;"><i class="bi bi-clock-history"></i> รายงานล่าสุด</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>หัวข้อ</th>
                        <th>ผู้รายงาน</th>
                        <th>สถานะ</th>
                        <th>วันที่สร้าง</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReports as $report)
                        <tr>
                            <td>{{ $report->topic ?? '-' }}</td>
                            <td>{{ $report->user->full_name ?? $report->reporter_name ?? '-' }}</td>
                            <td><span class="badge bg-{{ $report->status_color }}">{{ $report->status_label }}</span></td>
                            <td>{{ $report->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">ยังไม่มีรายงานในระบบ</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
