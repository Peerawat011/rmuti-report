@extends('layouts.app')

@section('title', 'แก้ไขรายงาน')

@section('content')
<div class="container py-4" style="max-width: 800px;">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="link-gov">จัดการรายงาน</a></li>
            <li class="breadcrumb-item active">แก้ไขรายงาน #{{ $report->id }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0" style="color: #EF6C00;"><i class="bi bi-pencil-square"></i> แก้ไขรายงาน #{{ $report->id }}</h4>
        <span class="badge bg-{{ $report->status_color }}">{{ $report->status_label }}</span>
    </div>

    <form action="{{ route('admin.reports.update', $report) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.reports._form')

        <div class="d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> ยกเลิก</a>
            <button type="submit" class="btn btn-gov"><i class="bi bi-check2-circle"></i> บันทึกการแก้ไข</button>
        </div>
    </form>

</div>
@endsection
