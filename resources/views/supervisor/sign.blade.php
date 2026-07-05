@extends('layouts.app')

@section('title', 'ลงนามอนุมัติ - รายงาน #' . $report->id)

@section('content')
<div class="container py-4" style="max-width: 750px;">

    <nav class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supervisor.inbox') }}" class="link-gov">กล่องรายงาน</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.show', $report) }}" class="link-gov">รายงาน #{{ $report->id }}</a></li>
            <li class="breadcrumb-item active">ลงนามอนุมัติ</li>
        </ol>
    </nav>

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Card หลัก --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-3" style="border-bottom: 2px solid #F57C00;">
            <h5 class="mb-0" style="color: #EF6C00;">
                <i class="bi bi-pen-fill"></i> ลงนามอนุมัติ — รายงาน #{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}
            </h5>
        </div>
        <div class="card-body">
            <div class="alert" style="background: #FFF3E0; border: 1px solid #FFB74D; color: #BF360C;">
                <strong><i class="bi bi-shield-check-fill"></i> คุณเป็นผู้บังคับบัญชาลำดับที่ {{ $role }}</strong>
                <div class="small mt-1">กรุณาตรวจสอบรายงานและให้ความเห็นก่อนลงนาม</div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted">หัวข้อ</small>
                    <div><strong>{{ $report->topic }}</strong></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">ผู้รายงาน</small>
                    <div><strong>{{ $report->reporter_name }}</strong> — {{ $report->reporter_position }}</div>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-eye"></i> ดูรายละเอียดรายงานเต็ม
                </a>
            </div>
        </div>
    </div>

    {{-- ส่งกลับแก้ไข --}}
    <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #DC3545 !important;">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong class="text-danger"><i class="bi bi-arrow-return-left"></i> รายงานยังไม่สมบูรณ์?</strong>
                    <div class="small text-muted">ส่งกลับให้ผู้รายงานแก้ไข พร้อมระบุเหตุผล — ระบบจะแจ้งเตือนผู้รายงานให้ทราบ</div>
                </div>
                <button class="btn btn-outline-danger btn-sm" type="button"
                        data-bs-toggle="collapse" data-bs-target="#rejectBox">
                    <i class="bi bi-arrow-return-left"></i> ส่งกลับแก้ไข
                </button>
            </div>

            <div class="collapse mt-3" id="rejectBox">
                <form action="{{ route('supervisor.reject', $report) }}" method="POST"
                      onsubmit="return confirm('ยืนยันส่งรายงานกลับให้แก้ไข?\n\nลายเซ็นทั้งหมดในรายงานนี้จะถูกลบ และผู้รายงานต้องลงนามใหม่หลังแก้ไข');">
                    @csrf
                    <label for="reject_reason" class="form-label fw-medium">
                        เหตุผลที่ส่งกลับแก้ไข <span class="text-danger">*</span>
                    </label>
                    <textarea name="reject_reason" id="reject_reason" class="form-control" rows="3"
                              placeholder="เช่น รายละเอียดข้อ ๔ ยังไม่ครบถ้วน กรุณาเพิ่มเติมสิ่งที่ได้เรียนรู้..."
                              required>{{ old('reject_reason') }}</textarea>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-send-fill"></i> ยืนยันส่งกลับแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ฟอร์มลงนาม --}}
    <form action="{{ route('supervisor.sign.store', $report) }}" method="POST" enctype="multipart/form-data" id="signatureForm">
        @csrf

        {{-- ความเห็น --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0" style="color: #EF6C00;">
                    <i class="bi bi-chat-left-text-fill"></i> ความเห็นของผู้บังคับบัญชา
                    <small class="text-muted">— ไม่บังคับ</small>
                </h6>
            </div>
            <div class="card-body">
                <textarea name="comment" class="form-control" rows="5"
                placeholder="กรอกความเห็นต่อรายงานนี้ (ไม่บังคับ — เช่น เห็นชอบ, ควรเผยแพร่ผลให้บุคลากรอื่น)">{{ old('comment') }}</textarea>
            </div>
        </div>

        {{-- ลายเซ็น (ใช้ template เดียวกับ reporter) --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0" style="color: #EF6C00;">
                    <i class="bi bi-signature"></i> ลายเซ็นของคุณ
                </h6>
            </div>
            <div class="card-body">

                @php $mySignature = Auth::user()->activeSignature; @endphp

                {{-- Mode toggle --}}
                <div class="btn-group w-100 mb-3" role="group">
                    @if($mySignature)
                        <input type="radio" class="btn-check" name="signature_mode" id="modeProfile" value="profile" checked>
                        <label class="btn btn-outline-primary" for="modeProfile">
                            <i class="bi bi-star-fill"></i> ลายเซ็นประจำตัว
                        </label>
                    @endif
                    <input type="radio" class="btn-check" name="signature_mode" id="modeDraw" value="draw" {{ $mySignature ? '' : 'checked' }}>
                    <label class="btn btn-outline-primary" for="modeDraw">
                        <i class="bi bi-pencil-fill"></i> วาดใหม่
                    </label>
                    <input type="radio" class="btn-check" name="signature_mode" id="modeUpload" value="upload">
                    <label class="btn btn-outline-primary" for="modeUpload">
                        <i class="bi bi-upload"></i> อัปโหลดรูป
                    </label>
                </div>

                {{-- Profile signature section --}}
                @if($mySignature)
                    <div id="profileSection">
                        <div class="text-center p-3 border rounded" style="border: 2px solid #A5D6A7 !important; background: #F7FBF7;">
                            <img src="{{ route('profile.signature.preview') }}?v={{ $mySignature->id }}"
                                 alt="ลายเซ็นประจำตัว" style="max-height: 120px; max-width: 100%;">
                            <div class="small text-muted mt-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                ใช้ลายเซ็นประจำตัวของคุณ (แนะนำ — ไม่ต้องวาดใหม่ทุกครั้ง)
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> หากวาดใหม่หรืออัปโหลด ระบบจะบันทึกเป็นลายเซ็นประจำตัวเวอร์ชันใหม่ให้อัตโนมัติ
                        </small>
                    </div>
                @endif

                {{-- Draw section --}}
                <div id="drawSection" @if($mySignature) style="display: none;" @endif>
                    <div class="border rounded" style="border: 2px dashed #FFB74D !important; background: #FFFEF8;">
                        <canvas id="signaturePad" style="width: 100%; height: 200px; cursor: crosshair; touch-action: none;"></canvas>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle"></i> ใช้เมาส์หรือนิ้ววาดลายเซ็นในกรอบด้านบน
                    </small>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clearBtn">
                        <i class="bi bi-eraser"></i> ล้าง
                    </button>
                    <input type="hidden" name="signature_data" id="signatureData">
                </div>

                {{-- Upload section --}}
                <div id="uploadSection" style="display: none;">
                    <input type="file" class="form-control" id="signature_file" name="signature_file"
                           accept="image/jpeg,image/png,image/jpg" onchange="previewUpload(event)">
                    <small class="text-muted">รองรับ JPG, PNG • ไม่เกิน 2 MB</small>
                    <div id="uploadPreview" class="text-center mt-2" style="display: none;">
                        <img id="uploadPreviewImg" src=""
                             style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 6px; padding: 8px;">
                    </div>
                </div>

                <hr class="my-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">วันที่ลงนาม</label>
                        <input type="date" class="form-control" name="signed_date"
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('reports.show', $report) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> ยกเลิก
            </a>
            <button type="submit" class="btn btn-gov">
                <i class="bi bi-check2-circle"></i> ลงนามอนุมัติ (ลำดับที่ {{ $role }})
            </button>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('signaturePad');

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }

    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: '#000000',
        minWidth: 1.5,
        maxWidth: 3,
    });

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    document.getElementById('clearBtn').addEventListener('click', () => signaturePad.clear());

    const profileSection = document.getElementById('profileSection');

    function switchMode(mode) {
        if (profileSection) profileSection.style.display = mode === 'profile' ? 'block' : 'none';
        document.getElementById('drawSection').style.display = mode === 'draw' ? 'block' : 'none';
        document.getElementById('uploadSection').style.display = mode === 'upload' ? 'block' : 'none';
        if (mode === 'draw') resizeCanvas();   // canvas ที่เคยถูกซ่อนต้องปรับขนาดใหม่
    }

    document.querySelectorAll('input[name="signature_mode"]').forEach(function (radio) {
        radio.addEventListener('change', function () { switchMode(this.value); });
    });

    document.getElementById('signatureForm').addEventListener('submit', function (e) {
        const mode = document.querySelector('input[name="signature_mode"]:checked').value;
        if (mode === 'draw') {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('กรุณาวาดลายเซ็นก่อนบันทึก');
                return false;
            }
            document.getElementById('signatureData').value = signaturePad.toDataURL('image/png');
        }
    });
});

function previewUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('uploadPreviewImg').src = e.target.result;
        document.getElementById('uploadPreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>
@endsection