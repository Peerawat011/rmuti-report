@extends('layouts.app')

@section('title', 'รายงานที่รอลงนาม')

@section('content')
<div class="container py-4" style="max-width: 1100px;">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1" style="color: #EF6C00;">
                <i class="bi bi-inbox-fill"></i> รายงานที่รอลงนาม
            </h3>
            <small class="text-muted">รายการรายงานที่คุณเป็นผู้บังคับบัญชา</small>
        </div>
        <span class="badge bg-warning text-dark fs-6">
            <i class="bi bi-bell-fill"></i> {{ $reports->total() }} รายการ
        </span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($reports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #FFF3E0;">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>หัวข้อ / ผู้รายงาน</th>
                                <th style="width: 120px;">ลำดับของคุณ</th>
                                <th style="width: 150px;">สถานะ</th>
                                <th style="width: 100px;" class="text-center">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                @php
                                    $myRole = $report->getSupervisorRoleFor(Auth::id());
                                    $myRoleString = 'supervisor_' . $myRole;
                                    $iSigned = $report->hasSignatureFromRole($myRoleString);
                                @endphp
                                <tr>
                                    <td class="text-muted">#{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <strong>{{ Str::limit($report->topic, 50) }}</strong>
                                        <div class="text-muted small">
                                            <i class="bi bi-person-fill"></i>
                                            {{ $report->reporter_name }} —
                                            {{ $report->reporter_position }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">ลำดับที่ {{ $myRole }}</span>
                                    </td>
                                    <td>
                                        @if($iSigned)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle-fill"></i> เซ็นแล้ว
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock-fill"></i> รอลงนาม
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('reports.show', $report) }}"
                                           class="btn btn-sm btn-outline-primary" title="ดูรายงาน">
                                            <i class="bi bi-eye"></i> ดู
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">{{ $reports->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #FFB74D;"></i>
                    <h5 class="mt-3 text-muted">ยังไม่มีรายงานที่ต้องลงนาม</h5>
                    <p class="text-muted">เมื่อมีบุคลากรเลือกคุณเป็นผู้บังคับบัญชา รายงานจะแสดงที่นี่</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection