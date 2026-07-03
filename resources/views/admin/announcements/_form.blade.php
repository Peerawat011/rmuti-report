{{-- ฟอร์มประกาศ ใช้ร่วม create/edit --}}
{{-- ตัวแปร: $announcement (nullable) --}}
@php $announcement = $announcement ?? null; @endphp

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

{{-- หัวข้อ --}}
<div class="mb-3">
    <label for="title" class="form-label fw-medium"><i class="bi bi-card-heading text-primary"></i> หัวข้อประกาศ <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="title" name="title"
           value="{{ old('title', $announcement->title ?? '') }}"
           placeholder="เช่น กำหนดการลงทะเบียนเรียน ภาคเรียนที่ 1 ปีการศึกษา 2569" required autofocus>
</div>

{{-- เนื้อหา --}}
<div class="mb-3">
    <label for="content" class="form-label fw-medium"><i class="bi bi-text-paragraph text-primary"></i> เนื้อหา <span class="text-danger">*</span></label>
    <textarea class="form-control" id="content" name="content" rows="8"
              placeholder="รายละเอียดข่าวประชาสัมพันธ์..." required>{{ old('content', $announcement->content ?? '') }}</textarea>
    <small class="text-muted">ขึ้นบรรทัดใหม่ได้ ระบบจะคงรูปแบบบรรทัดไว้</small>
</div>

{{-- ลิงก์เว็บไซต์ภายนอก (ไม่บังคับ) --}}
<div class="mb-3">
    <label for="link_url" class="form-label fw-medium"><i class="bi bi-link-45deg text-primary"></i> ลิงก์เว็บไซต์ที่เกี่ยวข้อง <small class="text-muted">— ไม่บังคับ</small></label>
    <input type="text" class="form-control" id="link_url" name="link_url"
           value="{{ old('link_url', $announcement->link_url ?? '') }}"
           placeholder="เช่น https://ess-register.rmuti.ac.th/AppKK/">
    <small class="text-muted">ถ้ากรอก ระบบจะแสดงปุ่ม "ไปยังเว็บไซต์" ในประกาศ (เปิดในแท็บใหม่)</small>
</div>

{{-- รูปภาพเดิม (เฉพาะตอนแก้ไข) --}}
@if($announcement && $announcement->images->count() > 0)
    <div class="mb-3">
        <label class="form-label fw-medium"><i class="bi bi-images text-primary"></i> รูปภาพปัจจุบัน</label>
        <div class="row g-2">
            @foreach($announcement->images as $img)
                <div class="col-4 col-md-3">
                    <div class="position-relative border rounded p-1">
                        <img src="{{ $img->url }}" alt="" class="img-fluid rounded" style="height: 110px; width: 100%; object-fit: cover;">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $img->id }}" id="del_{{ $img->id }}">
                            <label class="form-check-label small text-danger" for="del_{{ $img->id }}">ลบรูปนี้</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- อัปโหลดรูปใหม่ (หลายไฟล์) --}}
<div class="mb-3">
    <label for="images" class="form-label fw-medium">
        <i class="bi bi-cloud-arrow-up-fill text-primary"></i>
        {{ $announcement ? 'เพิ่มรูปภาพ' : 'รูปภาพประกอบ' }} <small class="text-muted">(เลือกได้หลายไฟล์)</small>
    </label>
    <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
    <small class="text-muted">รองรับ JPG, PNG, WEBP — ไม่เกิน 4 MB ต่อไฟล์ (รูปแรกจะใช้เป็นรูปปก)</small>
</div>

{{-- สถานะเผยแพร่ --}}
<div class="mb-3 form-check form-switch">
    <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1"
           {{ old('is_published', $announcement->is_published ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_published">
        <i class="bi bi-broadcast text-success"></i> เผยแพร่ทันที (ปิดไว้ = บันทึกเป็นฉบับร่าง ไม่แสดงหน้าข่าว)
    </label>
</div>
