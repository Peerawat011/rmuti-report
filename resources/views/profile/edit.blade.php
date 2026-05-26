@extends('layouts.app')

@section('title', 'แก้ไขโปรไฟล์')

@section('content')
<div class="container py-4" style="max-width: 800px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="link-gov">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ route('profile.show') }}" class="link-gov">โปรไฟล์</a></li>
            <li class="breadcrumb-item active">แก้ไขข้อมูล</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===== ฟอร์มที่ 1: แก้ไขข้อมูลส่วนตัว ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0" style="color: #EF6C00;">
                <i class="bi bi-person-vcard-fill"></i> แก้ไขข้อมูลส่วนตัว
            </h5>
        </div>
        <div class="card-body p-4">

            @if($errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('position') || $errors->has('department') || $errors->has('faculty'))
                <div class="alert alert-danger py-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>พบข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-1" style="padding-left: 1.2rem;">
                        @foreach(['first_name', 'last_name', 'email', 'position', 'department', 'faculty'] as $field)
                            @error($field)<li>{{ $message }}</li>@enderror
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ชื่อ + นามสกุล --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">
                            <i class="bi bi-person-fill text-primary"></i> ชื่อ <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                               value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">
                            <i class="bi bi-person-fill text-primary"></i> นามสกุล <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                               value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                </div>

                {{-- อีเมล --}}
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope-fill text-primary"></i> อีเมล <span class="text-danger">*</span>
                    </label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email', $user->email) }}" required>
                </div>

                {{-- ตำแหน่งงาน --}}
                <div class="mb-3">
                    <label for="position" class="form-label">
                        <i class="bi bi-briefcase-fill text-primary"></i> ตำแหน่งงาน <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="position" name="position"
                           value="{{ old('position', $user->position) }}" required>
                </div>

                {{-- สังกัดสำนัก/สถาบัน/กอง --}}
                <div class="mb-3">
                    <label for="department" class="form-label">
                        <i class="bi bi-building-fill text-primary"></i> สังกัดสำนัก/สถาบัน/กอง <span class="text-danger">*</span>
                    </label>
                    @php
                        $departments = [
                            'สำนักงานอธิการบดี',
                            'สำนักส่งเสริมวิชาการและงานทะเบียน',
                            'สำนักวิทยบริการและเทคโนโลยีสารสนเทศ',
                            'สถาบันวิจัยและพัฒนา',
                            'กองคลัง',
                            'กองบริหารงานบุคคล',
                            'กองพัฒนานักศึกษา',
                            'อื่นๆ',
                        ];
                    @endphp
                    <select class="form-select" id="department" name="department" required>
                        <option value="">-- กรุณาเลือก --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ old('department', $user->department) == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- สังกัดคณะ --}}
                <div class="mb-3">
                    <label for="faculty" class="form-label">
                        <i class="bi bi-mortarboard-fill text-primary"></i> สังกัดคณะ <span class="text-danger">*</span>
                    </label>
                    @php
                        $faculties = [
                            'คณะวิศวกรรมศาสตร์',
                            'คณะบริหารธุรกิจ',
                            'คณะวิทยาศาสตร์และศิลปศาสตร์',
                            'คณะครุศาสตร์อุตสาหกรรม',
                            'คณะสถาปัตยกรรมศาสตร์และศิลปกรรมสร้างสรรค์',
                            'คณะศิลปกรรมและออกแบบอุตสาหกรรม',
                            'ไม่สังกัดคณะ',
                        ];
                    @endphp
                    <select class="form-select" id="faculty" name="faculty" required>
                        <option value="">-- กรุณาเลือก --</option>
                        @foreach($faculties as $fac)
                            <option value="{{ $fac }}" {{ old('faculty', $user->faculty) == $fac ? 'selected' : '' }}>
                                {{ $fac }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-gov">
                        <i class="bi bi-check2-circle"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== ฟอร์มที่ 2: เปลี่ยนรหัสผ่าน ===== --}}
    <div class="card border-0 shadow-sm" id="password-section">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0" style="color: #EF6C00;">
                <i class="bi bi-shield-lock-fill"></i> เปลี่ยนรหัสผ่าน
            </h5>
        </div>
        <div class="card-body p-4">

            @if($errors->has('current_password') || $errors->has('password'))
                <div class="alert alert-danger py-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    @error('current_password') {{ $message }} @enderror
                    @error('password') {{ $message }} @enderror
                </div>
            @endif

            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label">
                        <i class="bi bi-key-fill text-primary"></i> รหัสผ่านปัจจุบัน <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control" id="current_password" name="current_password"
                           placeholder="กรอกรหัสผ่านปัจจุบัน" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill text-primary"></i> รหัสผ่านใหม่ <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="อย่างน้อย 6 ตัวอักษร" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-shield-check text-primary"></i> ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                               placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" required>
                    </div>
                </div>

                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>คำแนะนำ:</strong> รหัสผ่านควรมีอย่างน้อย 6 ตัวอักษร และไม่ควรเหมือนรหัสเดิม
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="bi bi-key-fill"></i> เปลี่ยนรหัสผ่าน
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection