@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้ใหม่')

@section('content')
<div class="container py-4" style="max-width: 700px;">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="link-gov">จัดการผู้ใช้</a></li>
            <li class="breadcrumb-item active">เพิ่มผู้ใช้ใหม่</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0" style="color: #EF6C00;"><i class="bi bi-person-plus-fill"></i> เพิ่มผู้ใช้ใหม่</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                @include('admin.users._form', ['isEdit' => false])

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">ยกเลิก</a>
                    <button type="submit" class="btn btn-gov"><i class="bi bi-check-lg"></i> บันทึก</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
