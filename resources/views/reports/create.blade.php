@extends('layouts.app')

@section('title', 'สร้างรายงานใหม่')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    {{-- Breadcrumb --}}
    <nav class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="link-gov">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="link-gov">รายงานของฉัน</a></li>
            <li class="breadcrumb-item active">สร้างใหม่</li>
        </ol>
    </nav>

    <h3 class="mb-1" style="color: #EF6C00;">
        <i class="bi bi-file-earmark-plus-fill"></i> สร้างรายงานใหม่
    </h3>
    <p class="text-muted">แบบรายงานการพัฒนาบุคลากรโดยการอบรม/ศึกษาดูงาน/ประชุมสัมมนา</p>

    <form action="{{ route('reports.store') }}" method="POST">
        @csrf
        @include('reports._form')
    </form>

</div>
@endsection