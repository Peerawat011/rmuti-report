@extends('layouts.app')

@section('title', 'สร้างรายงานให้ผู้ใช้')

@section('content')
<div class="container py-4" style="max-width: 800px;">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}" class="link-gov">จัดการรายงาน</a></li>
            <li class="breadcrumb-item active">สร้างรายงานใหม่</li>
        </ol>
    </nav>

    <h4 class="mb-3" style="color: #EF6C00;"><i class="bi bi-plus-circle-fill"></i> สร้างรายงานให้ผู้ใช้</h4>

    <form action="{{ route('admin.reports.store') }}" method="POST">
        @csrf
        @include('admin.reports._form')

        <div class="d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> ยกเลิก</a>
            <button type="submit" class="btn btn-gov"><i class="bi bi-check2-circle"></i> บันทึกรายงาน</button>
        </div>
    </form>

</div>
@endsection
