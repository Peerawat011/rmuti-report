@extends('layouts.app')

@section('title', 'แก้ไขประกาศ')

@section('content')
<div class="container py-4" style="max-width: 760px;">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.announcements.index') }}" class="link-gov">จัดการประกาศ</a></li>
            <li class="breadcrumb-item active">แก้ไข</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0" style="color: #EF6C00;"><i class="bi bi-pencil-square"></i> แก้ไขประกาศ</h5>
            @if($announcement->is_published)
                <span class="badge bg-success">เผยแพร่</span>
            @else
                <span class="badge bg-secondary">ซ่อน</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.announcements._form')

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">ยกเลิก</a>
                    <button type="submit" class="btn btn-gov"><i class="bi bi-check-lg"></i> บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
