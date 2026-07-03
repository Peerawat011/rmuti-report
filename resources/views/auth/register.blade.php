@extends('layouts.app')

@section('title', 'ลงทะเบียนบุคลากร')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 600px;">

        <div class="auth-header">
            <i class="bi bi-person-badge-fill" style="font-size: 2rem;"></i>
            <h4 class="mt-2 mb-0">ลงทะเบียนบุคลากร</h4>
            <small style="opacity: 0.9;">Personnel Registration</small>
        </div>

        <div class="auth-body">

            @if($errors->any())
                <div class="alert alert-danger py-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>พบข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-1" style="padding-left: 1.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf

                {{-- ชื่อ + นามสกุล (อยู่บรรทัดเดียวกัน) --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">
                            <i class="bi bi-person-fill text-primary"></i> ชื่อ <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control"
                               id="first_name" name="first_name"
                               value="{{ old('first_name') }}"
                               placeholder="เช่น คอม"
                               required autofocus>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">
                            <i class="bi bi-person-fill text-primary"></i> นามสกุล <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control"
                               id="last_name" name="last_name"
                               value="{{ old('last_name') }}"
                               placeholder="เช่น พิวเตอร์"
                               required>
                    </div>
                </div>

                {{-- อีเมล --}}
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope-fill text-primary"></i> อีเมล <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-at"></i></span>
                        <input type="email"
                               class="form-control"
                               id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="example@rmuti.ac.th"
                               required>
                    </div>
                </div>

                {{-- ตำแหน่งงาน --}}
                <div class="mb-3">
                    <label for="position" class="form-label">
                        <i class="bi bi-briefcase-fill text-primary"></i> ตำแหน่งงาน <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control"
                           id="position" name="position"
                           value="{{ old('position') }}"
                           placeholder="เช่น อาจารย์, เจ้าหน้าที่ธุรการ, นักวิชาการคอมพิวเตอร์"
                           required>
                </div>

                {{-- สังกัดสำนัก/สถาบัน/กอง (กรอกเอง + มีตัวเลือกแนะนำ) --}}
                <div class="mb-3">
                    <label for="department" class="form-label">
                        <i class="bi bi-building-fill text-primary"></i> สังกัดสำนัก/สถาบัน/กอง <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="department" name="department" list="departmentList"
                           value="{{ old('department') }}"
                           placeholder="เช่น สำนักส่งเสริมวิชาการและงานทะเบียน" required>
                    <datalist id="departmentList">
                        <option value="สำนักงานอธิการบดี">
                        <option value="สำนักส่งเสริมวิชาการและงานทะเบียน">
                        <option value="สำนักวิทยบริการและเทคโนโลยีสารสนเทศ">
                        <option value="สถาบันวิจัยและพัฒนา">
                        <option value="กองคลัง">
                        <option value="กองบริหารงานบุคคล">
                        <option value="กองพัฒนานักศึกษา">
                        <option value="อื่นๆ">
                    </datalist>
                </div>

                {{-- สังกัดคณะ (กรอกเอง + มีตัวเลือกแนะนำ) --}}
                <div class="mb-3">
                    <label for="faculty" class="form-label">
                        <i class="bi bi-mortarboard-fill text-primary"></i> สังกัดคณะ <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="faculty" name="faculty" list="facultyList"
                           value="{{ old('faculty') }}"
                           placeholder="เช่น คณะวิศวกรรมศาสตร์" required>
                    <datalist id="facultyList">
                        <option value="คณะวิศวกรรมศาสตร์">
                        <option value="คณะบริหารธุรกิจ">
                        <option value="คณะวิทยาศาสตร์และศิลปศาสตร์">
                        <option value="คณะครุศาสตร์อุตสาหกรรม">
                        <option value="คณะสถาปัตยกรรมศาสตร์และศิลปกรรมสร้างสรรค์">
                        <option value="คณะศิลปกรรมและออกแบบอุตสาหกรรม">
                        <option value="ไม่สังกัดคณะ">
                    </datalist>
                </div>
                
                {{-- ประเภทผู้ใช้งาน --}}
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-person-badge-fill text-primary"></i> ประเภทผู้ใช้งาน <span class="text-danger">*</span>
                    </label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="role" id="roleStaff"
                                value="staff" {{ old('role', 'staff') == 'staff' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-primary w-100 py-2 text-start" for="roleStaff">
                                <i class="bi bi-person-fill" style="font-size: 1.2rem;"></i>
                                <strong class="ms-2">บุคลากรทั่วไป</strong>
                                <div class="small text-muted ms-4 mt-1">สร้าง/ส่งรายงาน</div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="role" id="roleSupervisor"
                                value="supervisor" {{ old('role') == 'supervisor' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-primary w-100 py-2 text-start" for="roleSupervisor">
                                <i class="bi bi-person-check-fill" style="font-size: 1.2rem;"></i>
                                <strong class="ms-2">ผู้บังคับบัญชา</strong>
                                <div class="small text-muted ms-4 mt-1">ตรวจ/ลงนาม</div>
                            </label>
                        </div>
                    </div>
                </div>
                <hr>

                {{-- รหัสผ่าน + ยืนยันรหัสผ่าน --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill text-primary"></i> รหัสผ่าน <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                               class="form-control"
                               id="password" name="password"
                               placeholder="อย่างน้อย 6 ตัวอักษร"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-shield-check text-primary"></i> ยืนยันรหัสผ่าน <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                               class="form-control"
                               id="password_confirmation" name="password_confirmation"
                               placeholder="กรอกรหัสผ่านอีกครั้ง"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-gov w-100">
                    <i class="bi bi-person-check-fill"></i> ลงทะเบียน
                </button>
            </form>

            <hr class="my-3">

            <div class="text-center">
                <small class="text-muted">มีบัญชีอยู่แล้ว?</small>
                <a href="{{ route('login') }}" class="link-gov">เข้าสู่ระบบ</a>
            </div>

        </div>
    </div>
</div>
@endsection