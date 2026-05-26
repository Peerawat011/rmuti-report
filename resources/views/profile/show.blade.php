@extends('layouts.app')

@section('title', 'โปรไฟล์ของฉัน')

@section('content')
<div class="container py-4" style="max-width: 800px;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Profile Card --}}
    <div class="card border-0 shadow-sm overflow-hidden">

        {{-- Header สีน้ำส้ม --}}
        <div style="background: linear-gradient(135deg, #EF6C00 0%, #FFB74D 100%); height: 120px;"></div>

        {{-- Avatar + ชื่อ --}}
<div class="text-center px-4" style="margin-top: -60px;">

    {{-- รูปโปรไฟล์ (หรือตัวอักษรย่อถ้าไม่มี) --}}
    <div class="position-relative d-inline-block">
        @if($user->avatar_url)
            <img src="{{ $user->avatar_url }}"
                 alt="Profile"
                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 5px solid #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        @else
            <div class="d-inline-flex align-items-center justify-content-center"
                 style="width: 120px; height: 120px; border-radius: 50%; background: #FFC107; color: #BF360C; font-size: 36px; font-weight: 600; border: 5px solid #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                {{ $user->initials }}
            </div>
        @endif

        {{-- ปุ่มกล้องลอยที่มุมขวาล่าง --}}
        <button type="button"
                class="position-absolute btn btn-gov rounded-circle d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px; bottom: 5px; right: 5px; border: 3px solid #ffffff; padding: 0;"
                data-bs-toggle="modal"
                data-bs-target="#avatarModal"
                title="เปลี่ยนรูปโปรไฟล์">
            <i class="bi bi-camera-fill"></i>
        </button>
    </div>
            <h3 class="mt-3 mb-1" style="color: #EF6C00;">
    {{ $user->first_name }} {{ $user->last_name }}
</h3>
<p class="mb-3" style="color: #6c757d;">
    <i class="bi bi-briefcase-fill" style="color: #000000;"></i> {{ $user->position }}
    <span class="mx-2">•</span>
    <i class="bi bi-mortarboard-fill" style="color: #000000;"></i> {{ $user->faculty }}
</p>

<p class="mb-3">
    <span class="badge bg-{{ $user->role_color }}" style="font-size: 0.85rem; padding: 6px 12px;">
        <i class="bi bi-{{ $user->isSupervisor() ? 'person-check-fill' : 'person-fill' }}"></i>
        {{ $user->role_label }}
    </span>
</p>

{{-- ปุ่ม Action --}}
<div class="d-flex gap-2 justify-content-center mb-4">
    <a href="{{ route('profile.edit') }}" class="btn btn-gov">
        <i class="bi bi-pencil-square"></i> แก้ไขข้อมูล
    </a>
    <a href="{{ route('profile.edit') }}#password-section" class="btn" style="background: transparent; color: #047513; border: 1px solid #14b405;">
        <i class="bi bi-key-fill"></i> เปลี่ยนรหัสผ่าน
    </a>
</div>
        </div>

        {{-- ข้อมูลส่วนตัว --}}
        <div class="card-body border-top">
            <h5 class="mb-3" style="color: #EF6C00;">
                <i class="bi bi-person-vcard-fill"></i> ข้อมูลส่วนตัว
            </h5>

            <div class="row g-3">
                

                

                <div class="col-md-6">
                    <small class="text-muted d-block">ชื่อ</small>
                    <strong>{{ $user->first_name }}</strong>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block">นามสกุล</small>
                    <strong>{{ $user->last_name }}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">อีเมล</small>
                    <strong>{{ $user->email }}</strong>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block">ตำแหน่งงาน</small>
                    <strong>{{ $user->position }}</strong>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block">สังกัดสำนัก/สถาบัน/กอง</small>
                    <strong>{{ $user->department }}</strong>
                </div>

                <div class="col-md-12">
                    <small class="text-muted d-block">สังกัดคณะ</small>
                    <strong>{{ $user->faculty }}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">รหัสบุคลากร</small>
                    <strong>#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</strong>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="mb-3" style="color: #EF6C00;">
                <i class="bi bi-clock-history"></i> ข้อมูลการใช้งาน
            </h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">สมัครสมาชิกเมื่อ</small>
                    <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">อัปเดตข้อมูลล่าสุด</small>
                    <strong>{{ $user->updated_at->format('d/m/Y H:i') }}</strong>
                </div>
            </div>
        </div>

    </div>
{{-- ===== Modal: Upload Avatar ===== --}}
    <div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header" style="background: #F57C00; color: #ffffff;">
                    <h5 class="modal-title">
                        <i class="bi bi-camera-fill"></i> เปลี่ยนรูปโปรไฟล์
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        @error('avatar')
                            <div class="alert alert-danger py-2">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                            </div>
                        @enderror

                        {{-- รูป Preview --}}
                        <div class="text-center mb-3">
                            <div id="avatarPreview"
                                 class="d-inline-flex align-items-center justify-content-center mx-auto"
                                 style="width: 150px; height: 150px; border-radius: 50%; background: #FFF3E0; border: 2px dashed #F57C00; color: #BF360C; font-size: 48px; overflow: hidden;">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="Current"
                                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    <i class="bi bi-person-fill"></i>
                                @endif
                            </div>
                        </div>

                        {{-- ช่องเลือกไฟล์ --}}
                        <div class="mb-3">
                            <label for="avatar" class="form-label">
                                <i class="bi bi-image text-primary"></i> เลือกรูปภาพ
                            </label>
                            <input type="file"
                                   class="form-control"
                                   id="avatar"
                                   name="avatar"
                                   accept="image/jpeg,image/png,image/jpg"
                                   onchange="previewAvatar(event)"
                                   required>
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle"></i>
                                รองรับ JPG, PNG • ขนาดไม่เกิน 2 MB
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        @if($user->avatar)
                            {{-- ฟอร์มแยกสำหรับลบรูป --}}
                            <button type="button"
                                    class="btn btn-outline-danger me-auto"
                                    onclick="document.getElementById('deleteAvatarForm').submit();">
                                <i class="bi bi-trash"></i> ลบรูปปัจจุบัน
                            </button>
                        @endif

                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            ยกเลิก
                        </button>
                        <button type="submit" class="btn btn-gov">
                            <i class="bi bi-upload"></i> อัปโหลด
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ฟอร์มซ่อนสำหรับ DELETE method --}}
    @if($user->avatar)
        <form id="deleteAvatarForm" action="{{ route('profile.avatar.delete') }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endif

    {{-- JavaScript: Preview รูปก่อนอัปโหลด --}}
    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview"
                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
            };
            reader.readAsDataURL(file);
        }
    </script>
</div>
@endsection