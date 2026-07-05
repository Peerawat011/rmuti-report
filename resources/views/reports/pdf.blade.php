@php
    // ฝังฟอนต์เป็น data URI (base64) — วิธีที่ DomPDF โหลดได้ชัวร์ทุกแพลตฟอร์ม
    // (การอ้าง path ไฟล์ตรงๆ มีปัญหากับ chroot/protocol ของ DomPDF บน Windows)
    $fontPath = storage_path('fonts');
    $thsarabunRegular = base64_encode(file_get_contents($fontPath . '/THSarabunNew.ttf'));
    $thsarabunBold    = base64_encode(file_get_contents($fontPath . '/THSarabunNew Bold.ttf'));
    // ฝังโลโก้เป็น base64 เช่นกัน (DomPDF บน Windows โหลด path รูปตรงๆ ไม่ได้)
    $logoBase64 = base64_encode(file_get_contents(public_path('images/logo-rmuti.png')));

    // helper: แปลงรูปใน storage/app/public เป็น data URI (ใช้กับรูปลายเซ็น)
    $imgDataUri = function ($relPath) {
        if (!$relPath) return null;
        $full = storage_path('app/public/' . $relPath);
        if (!is_file($full)) return null;
        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/png';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($full));
    };
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>แบบรายงานการพัฒนาบุคลากร</title>
    <style>
        @font-face {
            font-family: 'thsarabun';
            font-weight: normal;
            font-style: normal;
            src: url(data:font/truetype;charset=utf-8;base64,{{ $thsarabunRegular }}) format('truetype');
        }
        @font-face {
            font-family: 'thsarabun';
            font-weight: bold;
            font-style: normal;
            src: url(data:font/truetype;charset=utf-8;base64,{{ $thsarabunBold }}) format('truetype');
        }

        * {
            font-family: 'thsarabun', sans-serif;  
        }

        body {
            font-size: 14px;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: left;
            margin-bottom: 8px;
        }

        .header .logo {
            width: 42px;
            height: auto;
            vertical-align: middle;
        }

        .header .university-name {
            font-weight: bold;
            font-size: 15px;
            vertical-align: middle;
            margin-left: 8px;
        }

        .title-box {
            border: 1px solid #000;
            padding: 6px 12px;
            text-align: center;
            margin: 10px auto;
            width: 78%;
            font-weight: bold;
            font-size: 15px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 3px;
            font-size: 14px;
        }

        .field {
            margin-bottom: 3px;
            padding-left: 28px;   /* ย่อหน้าเนื้อหาใต้หัวข้อ */
        }

        .field-label {
            display: inline;
        }

        .field-value {
            display: inline;
            border-bottom: 1px dotted #555;
            padding: 0 6px;
            font-weight: normal;
        }

        .field-value-long {
            display: block;
            border-bottom: 1px dotted #555;
            padding: 2px 4px;
            min-height: 18px;
        }

        /* ช่องติ๊กสี่เหลี่ยม (หัวข้อ ๑, ๒) */
        .checkbox {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1px solid #000;
            position: relative;
            vertical-align: middle;
            margin: 0 4px 2px 0;
        }
        /* เครื่องหมายถูกวาดด้วย CSS (ฟอนต์ TH Sarabun ไม่มี ✓) */
        .checkbox-checked::after {
            content: "";
            position: absolute;
            left: 4px;
            top: 0;
            width: 4px;
            height: 8px;
            border: solid #000;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        /* ตัวเลือกแบบวงเล็บ ( ) — หัวข้อ ๒.๕ */
        .paren-check {
            position: relative;
            display: inline-block;
            width: 12px;
            height: 12px;
            vertical-align: middle;
        }
        .paren-check.on::after {
            content: "";
            position: absolute;
            left: 3px;
            top: -1px;
            width: 4px;
            height: 8px;
            border: solid #000;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .signature-section {
            margin-top: 30px;
            page-break-before: always;
        }

        .signature-block {
            margin: 10px 0;
            text-align: center;
        }

        /* บล็อกลายเซ็นชิดขวา (ตามต้นฉบับ) */
        .sign-right {
            width: 58%;
            margin-left: 42%;
            text-align: center;
        }

        .signature-image {
            max-height: 55px;
            max-width: 180px;
        }

        .signature-line {
            border-top: 1px dotted #000;
            width: 260px;
            margin: 0 auto;
            padding-top: 4px;
        }

        .certify {
            text-indent: 40px;
            margin-top: 12px;
            margin-bottom: 4px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mt-3 { margin-top: 14px; }
        .mb-2 { margin-bottom: 10px; }

        .comment-block {
            margin: 8px 0;
            min-height: 36px;
        }

        .comment-line {
            border-bottom: 1px dotted #555;
            min-height: 22px;
            padding: 2px 4px;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

{{-- ==================== หน้าที่ 1 ==================== --}}

@if($report->doc_number)
<div style="text-align: right; font-size: 13px;">เลขที่เอกสาร {{ $report->doc_number }}</div>
@endif

<div class="header">
    <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo" class="logo">
    <span class="university-name">มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน  วิทยาเขตขอนแก่น</span>
</div>

<div class="title-box">
    แบบรายงานการพัฒนาบุคลากรโดยการอบรม/ศึกษาดูงาน/ประชุมสัมมนา<br>
    มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น
</div>

{{-- ส่วนที่ 1: ข้อมูลบุคลากร --}}
<div class="section-title">
    ๑. ข้อมูลบุคลากรไปราชการ &emsp;&emsp;
    <span class="checkbox {{ $report->activity_type == 'อบรม' ? 'checkbox-checked' : '' }}"></span> อบรม &emsp;&emsp;
    <span class="checkbox {{ $report->activity_type == 'ศึกษาดูงาน' ? 'checkbox-checked' : '' }}"></span> ศึกษาดูงาน &emsp;&emsp;
    <span class="checkbox {{ $report->activity_type == 'ประชุมสัมมนา' ? 'checkbox-checked' : '' }}"></span> ประชุมสัมมนา
</div>

<div class="field">
    <span class="field-label">ชื่อ-สกุล</span>
    <span class="field-value">{{ $report->reporter_name }}</span>
    <span class="field-label">ตำแหน่ง</span>
    <span class="field-value">{{ $report->reporter_position }}</span>
</div>

<div class="field">
    <span class="field-label">สังกัดสำนัก/สถาบัน/กอง</span>
    <span class="field-value">{{ $report->reporter_department }}</span>
    <span class="field-label">คณะ</span>
    <span class="field-value">{{ $report->reporter_faculty }}</span>
</div>

<div class="field">มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น</div>

{{-- ส่วนที่ 2 --}}
<div class="section-title">
    ๒. เข้ารับการ &emsp;&emsp;
    <span class="checkbox {{ $report->activity_type == 'อบรม' ? 'checkbox-checked' : '' }}"></span> อบรม &emsp;&emsp;
    <span class="checkbox {{ $report->activity_type == 'ศึกษาดูงาน' ? 'checkbox-checked' : '' }}"></span> ศึกษาดูงาน &emsp;&emsp;
    <span class="checkbox {{ $report->activity_type == 'ประชุมสัมมนา' ? 'checkbox-checked' : '' }}"></span> ประชุมสัมมนา
</div>

<div class="field">
    <strong>๒.๑ หัวข้อเรื่อง</strong>
    <span class="field-value-long">{{ $report->topic }}</span>
</div>

<div class="field">
    <strong>๒.๒ ตามคำสั่ง/หนังสือ ที่</strong>
    <span class="field-value">{{ $report->order_number ?? '-' }}</span>
    <strong>ระหว่างวันที่</strong>
    <span class="field-value">{{ $report->start_date->format('d/m/Y') }}</span>
</div>

<div class="field">
    <strong>ถึงวันที่</strong>
    <span class="field-value">{{ $report->end_date->format('d/m/Y') }}</span>
    <strong>เป็นเวลารวมทั้งสิ้น</strong>
    <span class="field-value">{{ $report->total_days }}</span>
    <strong>วัน</strong>
</div>

<div class="field">
    <strong>๒.๓ สถานที่ อบรม/ศึกษาดูงาน/ประชุมสัมมนา</strong>
    <span class="field-value-long">{{ $report->location }}</span>
</div>

<div class="field">
    <strong>๒.๔ หน่วยงานดำเนินการ</strong>
    <span class="field-value-long">{{ $report->organizer }}</span>
</div>

<div class="field">
    <strong>๒.๕ การใช้ประโยชน์จากการอบรม/ศึกษาดูงาน/ประชุมสัมมนา</strong>
</div>

@php $usage = $report->usage_types ?? []; @endphp
<div class="field" style="padding-left: 52px;">
    ( <span class="paren-check {{ in_array('ปฏิบัติงานในหน้าที่รับผิดชอบ', $usage) ? 'on' : '' }}"></span> )
    ปฏิบัติงานในหน้าที่รับผิดชอบ
    &emsp;
    ( <span class="paren-check {{ in_array('ขยายผลแก่บุคลากรในสถานศึกษา', $usage) ? 'on' : '' }}"></span> )
    ขยายผลแก่บุคลากรในสถานศึกษา
</div>

<div class="field" style="padding-left: 52px;">
    ( <span class="paren-check {{ in_array('อื่นๆ', $usage) ? 'on' : '' }}"></span> )
    อื่นๆ ระบุ
    <span class="field-value">{{ $report->usage_other ?? '' }}</span>
</div>

{{-- ส่วนที่ 3 --}}
<div class="section-title">๓. เอกสาร/ตำรา/คู่มือ</div>
<div class="comment-block">
    @if($report->documents)
        <div class="comment-line">{{ $report->documents }}</div>
    @else
        <div class="comment-line">-</div>
        <div class="comment-line">&nbsp;</div>
    @endif
</div>

{{-- ส่วนที่ 4 --}}
<div class="section-title">
    ๔. รายละเอียดการไปศึกษา ฝึกอบรม ประชุม สัมมนา ฯลฯ ให้เขียนรายละเอียด
</div>
<div style="font-size: 13px; color: #555; margin-bottom: 4px;">
    โดยบรรยายสิ่งที่ได้สังเกตรู้เห็น หรือได้รับถ่ายทอดมาให้ชัดเจน
</div>
<div class="comment-block">
    <div style="white-space: pre-wrap; border-bottom: 1px dotted #555; padding: 4px;">{{ $report->details }}</div>
</div>

{{-- ส่วนที่ 5 --}}
<div class="section-title">๕. สรุปข้อคิดเห็น/ข้อเสนอแนะ</div>
<div class="comment-block">
    <div style="white-space: pre-wrap; border-bottom: 1px dotted #555; padding: 4px;">{{ $report->suggestions }}</div>
</div>

{{-- ==================== หน้าที่ 2: ลายเซ็น ==================== --}}
<div class="signature-section">

    <div class="header">
        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo" class="logo">
        <span class="university-name">มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน  วิทยาเขตขอนแก่น</span>
    </div>

    <div class="certify">ขอรับรองว่าข้อมูลที่รายงานข้างต้นเป็นจริงทุกประการ</div>

    {{-- ลายเซ็นผู้รายงาน (ชิดขวาตามต้นฉบับ) --}}
    @php $reporterSig = $report->signatures->where('role', 'reporter')->first(); @endphp
    <div class="sign-right">
        @if($reporterSig && $imgDataUri($reporterSig->signature_image))
            <img src="{{ $imgDataUri($reporterSig->signature_image) }}" class="signature-image">
        @else
            <div style="height: 55px;"></div>
        @endif
        <div class="signature-line">
            ลงชื่อ {{ $reporterSig->signer_name ?? '.....................................' }} ผู้รายงาน
        </div>
        <div>({{ $reporterSig->signer_name ?? '.....................................' }})</div>
        <div>ตำแหน่ง {{ $reporterSig->signer_position ?? '.....................................' }}</div>
        <div>
            วันที่ {{ $reporterSig ? $reporterSig->signed_date->format('d') : '..........' }}
            เดือน {{ $reporterSig ? $reporterSig->signed_date->format('m') : '..........' }}
            พ.ศ. {{ $reporterSig ? ($reporterSig->signed_date->year + 543) : '............' }}
        </div>
    </div>

    {{-- ความเห็นผู้บังคับบัญชา --}}
    <div class="section-title">ความเห็นผู้บังคับบัญชา (เสนอตามลำดับชั้น)</div>

    @foreach([1, 2, 3] as $level)
        @php
            $sup = $report->{"supervisor{$level}"};
            $sig = $report->signatures->where('role', 'supervisor_' . $level)->first();
        @endphp

        @if($sup)
            <div class="comment-block">
                {{-- ความเห็น --}}
                <div class="comment-line">
                    {{ $sig->comment ?? '' }}
                </div>
                <div class="comment-line">&nbsp;</div>

                {{-- ลายเซ็น (ชิดขวาตามต้นฉบับ) --}}
                <div class="sign-right" style="margin-top: 4px;">
                    @if($sig && $imgDataUri($sig->signature_image))
                        <img src="{{ $imgDataUri($sig->signature_image) }}" class="signature-image">
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    <div class="signature-line">
                        ลงชื่อ {{ $sig->signer_name ?? '.....................................' }}
                    </div>
                    <div>({{ $sig->signer_name ?? '.....................................' }})</div>
                    <div>ตำแหน่ง {{ $sig->signer_position ?? '.....................................' }}</div>
                </div>
            </div>
        @endif
    @endforeach

</div>

</body>
</html>