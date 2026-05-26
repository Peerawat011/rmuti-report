@extends('layouts.app')

@section('title', 'แก้ไขรายงาน')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    <nav class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="link-gov">รายงานของฉัน</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.show', $report) }}" class="link-gov">รายงาน #{{ $report->id }}</a></li>
            <li class="breadcrumb-item active">แก้ไข</li>
        </ol>
    </nav>

    <h3 class="mb-1" style="color: #EF6C00;">
        <i class="bi bi-pencil-square"></i> แก้ไขรายงาน #{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}
    </h3>
    <p class="text-muted">แก้ไขข้อมูลแบบรายงาน</p>

    @php $user = Auth::user(); @endphp
    <form action="{{ route('reports.update', $report) }}" method="POST">
        @csrf
        @method('PUT')
        @include('reports._form')
    </form>

</div>
@endsection