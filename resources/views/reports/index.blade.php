@extends('layouts.app')

@section('title', 'รายงานของฉัน')

@section('content')
<div class="container py-4" style="max-width: 1100px;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if(($filter ?? null) === 'signed')
                <h3 class="mb-1" style="color: #198754;">
                    <i class="bi bi-patch-check-fill"></i> รายงานที่ลงนามแล้ว
                </h3>
                <small class="text-muted">เฉพาะรายงานของฉันที่ผู้บังคับบัญชาลงนามแล้ว — เรียงตามการลงนามล่าสุด</small>
            @else
                <h3 class="mb-1" style="color: #EF6C00;">
                    <i class="bi bi-file-earmark-text-fill"></i> รายงานของฉัน
                </h3>
                <small class="text-muted">แบบรายงานการพัฒนาบุคลากรโดยการอบรมศึกษาดูงานประชุมสัมมนา</small>
            @endif
        </div>
        <div class="d-flex gap-2">
            @if(($filter ?? null) === 'signed')
                <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-list-ul"></i> ดูรายงานทั้งหมด
                </a>
            @else
                <a href="{{ route('reports.index', ['filter' => 'signed']) }}" class="btn btn-outline-primary">
                    <i class="bi bi-patch-check"></i> ที่ลงนามแล้ว
                </a>
                <a href="{{ route('reports.create') }}" class="btn btn-gov">
                    <i class="bi bi-plus-circle-fill"></i> สร้างรายงานใหม่
                </a>
            @endif
        </div>
    </div>

    {{-- แถบเตือน: มีรายงานถูกส่งกลับให้แก้ไข --}}
    @if(($myRevisionCount ?? 0) > 0 && ($filter ?? null) !== 'signed')
        <div class="alert alert-danger d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <strong><i class="bi bi-arrow-return-left"></i> มีรายงานถูกส่งกลับให้แก้ไข {{ $myRevisionCount }} ฉบับ</strong>
                <div class="small">มองหาสถานะ <span class="badge bg-danger">ส่งกลับแก้ไข</span> ในตารางด้านล่าง — เปิดดูเหตุผล แก้ไข แล้วลงนามส่งใหม่</div>
            </div>
        </div>
    @endif

    {{-- Reports Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($reports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #FFF3E0;">
                            <tr>
                                <th style="width: 115px;">เลขที่</th>
                                <th>หัวข้อ</th>
                                <th style="width: 140px;">ประเภท</th>
                                <th style="width: 180px;">ระยะเวลา</th>
                                <th style="width: 130px;">สถานะ</th>
                                <th style="width: 180px;" class="text-center">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td class="text-muted text-nowrap">{{ $report->doc_number ?? '#' . str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <strong>{{ Str::limit($report->topic, 60) }}</strong>
                                        <div class="text-muted small">{{ $report->organizer }}</div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #FFB74D; color: #4E342E;">
                                            {{ $report->activity_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $report->start_date->format('d/m/Y') }} -
                                            {{ $report->end_date->format('d/m/Y') }}
                                            <div class="text-muted">({{ $report->total_days }} วัน)</div>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $report->status_color }}">
                                            {{ $report->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{-- ===== ปุ่ม PDF  ===== --}}
                                        <a href="{{ route('reports.pdf', $report) }}"
                                        class="btn btn-sm btn-outline-danger" title="ดาวน์โหลด PDF" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <a href="{{ route('reports.show', $report) }}"
                                           class="btn btn-sm btn-outline-primary" title="ดูรายงาน">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('reports.edit', $report) }}"
                                           class="btn btn-sm btn-outline-warning" title="แก้ไข">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('reports.destroy', $report) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('ยืนยันการลบรายงานนี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger" title="ลบ">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-3 border-top">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #FFB74D;"></i>
                    <h5 class="mt-3 text-muted">ยังไม่มีรายงาน</h5>
                    <p class="text-muted">คลิกปุ่ม "สร้างรายงานใหม่" เพื่อเริ่มต้น</p>
                    <a href="{{ route('reports.create') }}" class="btn btn-gov mt-2">
                        <i class="bi bi-plus-circle"></i> สร้างรายงานแรก
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection