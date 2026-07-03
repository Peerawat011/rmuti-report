{{-- ฟอร์มผู้ใช้ ใช้ร่วมกันทั้งหน้าเพิ่มและแก้ไข --}}
{{-- ตัวแปร: $user (อาจเป็น null ตอนสร้าง), $isEdit (bool) --}}
@php
    $user = $user ?? null;
    $isEdit = $isEdit ?? false;
@endphp

@if($errors->any())
    <div class="alert alert-danger py-2">
        <i class="bi bi-exclamation-triangle-fill"></i> <strong>พบข้อผิดพลาด:</strong>
        <ul class="mb-0 mt-1" style="padding-left: 1.2rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ชื่อ + นามสกุล --}}
<div class="row g-2 mb-3">
    <div class="col-md-6">
        <label for="first_name" class="form-label"><i class="bi bi-person-fill text-primary"></i> ชื่อ <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="first_name" name="first_name"
               value="{{ old('first_name', $user->first_name ?? '') }}" required autofocus>
    </div>
    <div class="col-md-6">
        <label for="last_name" class="form-label"><i class="bi bi-person-fill text-primary"></i> นามสกุล <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="last_name" name="last_name"
               value="{{ old('last_name', $user->last_name ?? '') }}" required>
    </div>
</div>

{{-- อีเมล --}}
<div class="mb-3">
    <label for="email" class="form-label"><i class="bi bi-envelope-fill text-primary"></i> อีเมล <span class="text-danger">*</span></label>
    <input type="email" class="form-control" id="email" name="email"
           value="{{ old('email', $user->email ?? '') }}" placeholder="example@rmuti.ac.th" required>
</div>

{{-- ตำแหน่งงาน --}}
<div class="mb-3">
    <label for="position" class="form-label"><i class="bi bi-briefcase-fill text-primary"></i> ตำแหน่งงาน <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="position" name="position"
           value="{{ old('position', $user->position ?? '') }}"
           placeholder="เช่น อาจารย์, เจ้าหน้าที่ธุรการ" required>
</div>

{{-- สังกัดสำนัก/สถาบัน/กอง (datalist = พิมพ์ได้อิสระ + มีตัวเลือกแนะนำ) --}}
<div class="mb-3">
    <label for="department" class="form-label"><i class="bi bi-building-fill text-primary"></i> สังกัดสำนัก/สถาบัน/กอง <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="department" name="department" list="departmentList"
           value="{{ old('department', $user->department ?? '') }}" required>
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

{{-- สังกัดคณะ --}}
<div class="mb-3">
    <label for="faculty" class="form-label"><i class="bi bi-mortarboard-fill text-primary"></i> สังกัดคณะ <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="faculty" name="faculty" list="facultyList"
           value="{{ old('faculty', $user->faculty ?? '') }}" required>
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

{{-- ประเภทผู้ใช้งาน (role) --}}
@php $currentRole = old('role', $user->role ?? 'staff'); @endphp
<div class="mb-3">
    <label class="form-label"><i class="bi bi-person-badge-fill text-primary"></i> ประเภทผู้ใช้งาน <span class="text-danger">*</span></label>
    <select class="form-select" name="role" required>
        <option value="staff" {{ $currentRole == 'staff' ? 'selected' : '' }}>บุคลากรทั่วไป (สร้าง/ส่งรายงาน)</option>
        <option value="supervisor" {{ $currentRole == 'supervisor' ? 'selected' : '' }}>ผู้บังคับบัญชา (ตรวจ/ลงนาม)</option>
        <option value="admin" {{ $currentRole == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (จัดการทั้งหมด)</option>
    </select>
    @if($isEdit && $user && $user->id === auth()->id())
        <small class="text-muted"><i class="bi bi-info-circle"></i> นี่คือบัญชีของคุณ — ไม่สามารถลดสิทธิ์ตัวเองได้</small>
    @endif
</div>

<hr>

{{-- รหัสผ่าน --}}
<div class="row g-2 mb-3">
    <div class="col-md-6">
        <label for="password" class="form-label">
            <i class="bi bi-lock-fill text-primary"></i> รหัสผ่าน
            @if($isEdit)
                <small class="text-muted">(เว้นว่าง = ไม่เปลี่ยน)</small>
            @else
                <span class="text-danger">*</span>
            @endif
        </label>
        <input type="password" class="form-control" id="password" name="password"
               placeholder="อย่างน้อย 6 ตัวอักษร" {{ $isEdit ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <label for="password_confirmation" class="form-label"><i class="bi bi-shield-check text-primary"></i> ยืนยันรหัสผ่าน</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
               placeholder="กรอกรหัสผ่านอีกครั้ง">
    </div>
</div>
