@extends('layouts.app')

@section('title', 'จัดการผู้ใช้')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="mb-1" style="color: #EF6C00;"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</h3>
            <p class="text-muted mb-0">ทั้งหมด {{ $users->total() }} คน</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="bi bi-speedometer2"></i> แดชบอร์ด
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-gov">
                <i class="bi bi-person-plus-fill"></i> เพิ่มผู้ใช้
            </a>
        </div>
    </div>

    {{-- ค้นหา + กรอง --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" value="{{ $search }}"
                               placeholder="ค้นหาชื่อ, อีเมล, ตำแหน่ง...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">-- ทุกประเภท --</option>
                        <option value="staff" {{ $role == 'staff' ? 'selected' : '' }}>บุคลากรทั่วไป</option>
                        <option value="supervisor" {{ $role == 'supervisor' ? 'selected' : '' }}>ผู้บังคับบัญชา</option>
                        <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-gov"><i class="bi bi-funnel-fill"></i> ค้นหา</button>
                </div>
            </div>
        </div>
    </form>

    {{-- ตาราง --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ผู้ใช้</th>
                        <th>ตำแหน่ง / สังกัด</th>
                        <th>ประเภท</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($u->avatar_url)
                                        <img src="{{ $u->avatar_url }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                    @else
                                        <div style="width:36px;height:36px;border-radius:50%;background:#FFC107;color:#BF360C;display:inline-flex;align-items:center;justify-content:center;font-weight:600;">{{ $u->initials }}</div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $u->full_name }}
                                            @if($u->id === auth()->id())
                                                <span class="badge bg-light text-dark border">คุณ</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $u->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $u->position }}</div>
                                <small class="text-muted">{{ $u->faculty }}</small>
                            </td>
                            <td><span class="badge bg-{{ $u->role_color }}">{{ $u->role_label }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i> แก้ไข
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('ยืนยันลบผู้ใช้ {{ $u->full_name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">ไม่พบผู้ใช้ที่ตรงเงื่อนไข</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $users->links() }}
    </div>

</div>
@endsection
