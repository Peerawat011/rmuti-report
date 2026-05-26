@extends('layouts.app')

@section('title', 'ลงนามรายงาน #' . $report->id)

@section('content')
<div class="container py-4" style="max-width: 700px;">

    {{-- Breadcrumb --}}
    <nav class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="link-gov">รายงานของฉัน</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.show', $report) }}" class="link-gov">รายงาน #{{ $report->id }}</a></li>
            <li class="breadcrumb-item active">ลงนาม</li>
        </ol>
    </nav>

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card: คำรับรอง --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-3" style="border-bottom: 2px solid #F57C00;">
            <h5 class="mb-0" style="color: #EF6C00;">
                <i class="bi bi-pen-fill"></i> ลงนามรายงาน #{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}
            </h5>
        </div>
        <div class="card-body">
            <div class="alert" style="background: #FFF3E0; border: 1px solid #FFB74D; color: #BF360C;">
                <i class="bi bi-shield-check-fill"></i>
                <strong>ขอรับรองว่าข้อมูลที่รายงานข้างต้นเป็นจริงทุกประการ</strong>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <small class="text-muted">ผู้รายงาน</small>
                    <div><strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">ตำแหน่ง</small>
                    <div><strong>{{ Auth::user()->position }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card: ฟอร์มลงนาม --}}
    <form action="{{ route('reports.sign.store', $report) }}" method="POST" enctype="multipart/form-data" id="signatureForm">
        @csrf

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0" style="color: #EF6C00;">
                    <i class="bi bi-signature"></i> ลายเซ็นของคุณ
                </h6>
            </div>
            <div class="card-body">

                {{-- เลือก Mode --}}
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="signature_mode" id="modeDraw" value="draw" checked>
                    <label class="btn btn-outline-primary" for="modeDraw">
                        <i class="bi bi-pencil-fill"></i> วาดลายเซ็น
                    </label>

                    <input type="radio" class="btn-check" name="signature_mode" id="modeUpload" value="upload">
                    <label class="btn btn-outline-primary" for="modeUpload">
                        <i class="bi bi-upload"></i> อัปโหลดรูป
                    </label>
                </div>

                {{-- ==================== Mode: วาด ==================== --}}
                <div id="drawSection">
                    <div class="border rounded" style="border: 2px dashed #FFB74D !important; background: #FFFEF8;">
                        <canvas id="signaturePad" style="width: 100%; height: 200px; cursor: crosshair; touch-action: none;"></canvas>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle"></i> ใช้เมาส์หรือนิ้วมือ (บนมือถือ) วาดลายเซ็นในกรอบด้านบน
                    </small>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearBtn">
                            <i class="bi bi-eraser"></i> ล้าง
                        </button>
                    </div>
                    {{-- Hidden field เก็บ base64 data --}}
                    <input type="hidden" name="signature_data" id="signatureData">
                </div>

                {{-- ==================== Mode: อัปโหลด ==================== --}}
                <div id="uploadSection" style="display: none;">
                    <div class="mb-3">
                        <label for="signature_file" class="form-label">เลือกไฟล์รูปลายเซ็น</label>
                        <input type="file" class="form-control" id="signature_file" name="signature_file"
                               accept="image/jpeg,image/png,image/jpg" onchange="previewUpload(event)">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> รองรับ JPG, PNG • ไม่เกิน 2 MB • แนะนำพื้นหลังโปร่งใส (PNG)
                        </small>
                    </div>

                    {{-- Preview --}}
                    <div id="uploadPreview" class="text-center" style="display: none;">
                        <p class="mb-2"><small class="text-muted">ตัวอย่าง:</small></p>
                        <img id="uploadPreviewImg" src=""
                             style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 6px; padding: 8px; background: white;">
                    </div>
                </div>

                {{-- วันที่ลงนาม --}}
                <hr class="my-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">วันที่ลงนาม</label>
                        <input type="date" class="form-control" name="signed_date"
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>

            </div>
        </div>

        {{-- ปุ่ม --}}
        <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('reports.show', $report) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> ยกเลิก
            </a>
            <button type="submit" class="btn btn-gov" id="submitBtn">
                <i class="bi bi-check2-circle"></i> บันทึกลายเซ็น
            </button>
        </div>
    </form>

</div>

{{-- ==================== JavaScript ==================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ========== SETUP CANVAS ==========
    const canvas = document.getElementById('signaturePad');
    const ctx = canvas.getContext('2d');

    // Resize canvas ให้พอดี container
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }

    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',  // โปร่งใส
        penColor: '#000000',
        minWidth: 1.5,
        maxWidth: 3,
    });

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // ========== ปุ่มล้าง ==========
    document.getElementById('clearBtn').addEventListener('click', function () {
        signaturePad.clear();
    });

    // ========== เปลี่ยน Mode ==========
    const modeDraw = document.getElementById('modeDraw');
    const modeUpload = document.getElementById('modeUpload');
    const drawSection = document.getElementById('drawSection');
    const uploadSection = document.getElementById('uploadSection');

    modeDraw.addEventListener('change', function () {
        if (this.checked) {
            drawSection.style.display = 'block';
            uploadSection.style.display = 'none';
        }
    });

    modeUpload.addEventListener('change', function () {
        if (this.checked) {
            drawSection.style.display = 'none';
            uploadSection.style.display = 'block';
        }
    });

    // ========== ตอน Submit ฟอร์ม ==========
    document.getElementById('signatureForm').addEventListener('submit', function (e) {
        const mode = document.querySelector('input[name="signature_mode"]:checked').value;

        if (mode === 'draw') {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('กรุณาวาดลายเซ็นก่อนบันทึก');
                return false;
            }
            // เก็บ base64 ลง hidden field
            document.getElementById('signatureData').value = signaturePad.toDataURL('image/png');
        }
    });
});

// ========== Preview รูปอัปโหลด ==========
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