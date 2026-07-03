@extends('layouts.app')

@section('title', 'แก้ไขผู้ใช้')

@section('content')
<div class="container py-4" style="max-width: 700px;">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="link-gov">จัดการผู้ใช้</a></li>
            <li class="breadcrumb-item active">แก้ไข: {{ $user->full_name }}</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0" style="color: #EF6C00;"><i class="bi bi-pencil-square"></i> แก้ไขผู้ใช้</h5>
            <span class="badge bg-{{ $user->role_color }}">{{ $user->role_label }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.users._form', ['isEdit' => true])

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">ยกเลิก</a>
                    <button type="submit" class="btn btn-gov"><i class="bi bi-check-lg"></i> บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
