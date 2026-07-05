@extends('layouts.app')

@section('title', 'จัดการรายงานทั้งหมด')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="mb-1" style="color: #EF6C00;"><i class="bi bi-file-earmark-text-fill"></i> จัดการรายงานทั้งหมด</h3>
            <p class="text-muted mb-0">ทั้งหมด {{ $reports->total() }} รายงาน</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="bi bi-speedometer2"></i> แดชบอร์ด
            </a>
            <a href="{{ route('admin.reports.create') }}" class="btn btn-gov">
                <i class="bi bi-plus-circle-fill"></i> สร้างรายงานให้ผู้ใช้
            </a>
        </div>
    </div>

    {{-- ค้นหา + กรอง --}}
    <form method="GET" action="{{ route('admin.reports.index') }}" class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" value="{{ $search }}"
                               placeholder="ค้นหาหัวข้อ, ชื่อผู้รายงาน, เลขที่คำสั่ง...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>ร่าง</option>
                        <option value="pending_signature" {{ $status == 'pending_signature' ? 'selected' : '' }}>รอลงนาม</option>
                        <option value="signed" {{ $status == 'signed' ? 'selected' : '' }}>ลงนามแล้ว</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                        <option value="revision" {{ $status == 'revision' ? 'selected' : '' }}>ส่งกลับแก้ไข</option>
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
                        <th>หัวข้อ</th>
                        <th>เจ้าของ</th>
                        <th>ช่วงวันที่</th>
                        <th>สถานะ</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $r)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($r->topic, 50) ?: '-' }}</div>
                                <small class="text-muted">{{ $r->doc_number ? $r->doc_number . ' · ' : '' }}{{ $r->activity_type }}</small>
                            </td>
                            <td>{{ $r->user->full_name ?? $r->reporter_name ?? '-' }}</td>
                            <td>
                                <small>{{ optional($r->start_date)->format('d/m/Y') }} – {{ optional($r->end_date)->format('d/m/Y') }}</small>
                            </td>
                            <td><span class="badge bg-{{ $r->status_color }}">{{ $r->status_label }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('reports.show', $r) }}" class="btn btn-sm btn-outline-secondary" title="ดู">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('reports.pdf', $r) }}" class="btn btn-sm btn-outline-danger" title="PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <a href="{{ route('admin.reports.edit', $r) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.reports.destroy', $r) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('ยืนยันลบรายงานนี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">ไม่พบรายงานที่ตรงเงื่อนไข</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $reports->links() }}</div>

</div>
@endsection
