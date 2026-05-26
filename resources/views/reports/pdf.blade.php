<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>แบบรายงานการพัฒนาบุคลากร</title>
    <style>
        @font-face {
            font-family: 'thsarabun';
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'thsarabun';
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        }

        * {
            font-family: 'thsarabun', sans-serif;  
        }

        body {
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 60px;
            margin-bottom: 6px;
        }

        .header .university-name {
            font-weight: bold;
            font-size: 14px;
        }

        .title-box {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: center;
            margin: 14px auto;
            width: 75%;
            font-weight: bold;
            font-size: 15px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .field {
            margin-bottom: 6px;
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

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 4px;
            text-align: center;
            line-height: 11px;
            font-size: 12px;
            vertical-align: middle;
        }

        .checkbox-checked::before {
            content: "✓";
            font-weight: bold;
        }

        .signature-section {
            margin-top: 30px;
            page-break-before: always;
        }

        .signature-block {
            margin: 22px 0;
            text-align: center;
        }

        .signature-image {
            max-height: 60px;
            max-width: 200px;
        }

        .signature-line {
            border-top: 1px dotted #000;
            width: 280px;
            margin: 0 auto;
            padding-top: 4px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mt-3 { margin-top: 14px; }
        .mb-2 { margin-bottom: 10px; }

        .comment-block {
            margin: 18px 0;
            min-height: 60px;
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

<div class="header">
    <img src="{{ public_path('images/logo-rmuti.png') }}" alt="Logo">
    <div class="university-name">มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น</div>
</div>

<div class="title-box">
    แบบรายงานการพัฒนาบุคลากรโดยการอบรม/ศึกษาดูงาน/ประชุมสัมมนา<br>
    มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น
</div>

{{-- ส่วนที่ 1: ข้อมูลบุคลากร --}}
<div class="section-title">
    ๑. ข้อมูลบุคลากรไปราชการ
    <span class="checkbox {{ $report->activity_type == 'อบรม' ? 'checkbox-checked' : '' }}"></span> อบรม
    <span class="checkbox {{ $report->activity_type == 'ศึกษาดูงาน' ? 'checkbox-checked' : '' }}"></span> ศึกษาดูงาน
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
    ๒. เข้ารับการ
    <span class="checkbox {{ $report->activity_type == 'อบรม' ? 'checkbox-checked' : '' }}"></span> อบรม
    <span class="checkbox {{ $report->activity_type == 'ศึกษาดูงาน' ? 'checkbox-checked' : '' }}"></span> ศึกษาดูงาน
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
<div class="field" style="margin-left: 20px;">
    <span class="checkbox {{ in_array('ปฏิบัติงานในหน้าที่รับผิดชอบ', $usage) ? 'checkbox-checked' : '' }}"></span>
    ปฏิบัติงานในหน้าที่รับผิดชอบ
    &nbsp;&nbsp;
    <span class="checkbox {{ in_array('ขยายผลแก่บุคลากรในสถานศึกษา', $usage) ? 'checkbox-checked' : '' }}"></span>
    ขยายผลแก่บุคลากรในสถานศึกษา
</div>

<div class="field" style="margin-left: 20px;">
    <span class="checkbox {{ in_array('อื่นๆ', $usage) ? 'checkbox-checked' : '' }}"></span>
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
        <img src="{{ public_path('images/logo-rmuti.png') }}" alt="Logo">
        <div class="university-name">มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น</div>
    </div>

    <div class="mt-3" style="text-align: left;">
        <strong>ขอรับรองว่าข้อมูลที่รายงานข้างต้นเป็นจริงทุกประการ</strong>
    </div>

    {{-- ลายเซ็นผู้รายงาน --}}
    @php $reporterSig = $report->signatures->where('role', 'reporter')->first(); @endphp
    <div class="signature-block">
        @if($reporterSig)
            <img src="{{ public_path('storage/' . $reporterSig->signature_image) }}" class="signature-image">
        @else
            <div style="height: 60px;"></div>
        @endif
        <div class="signature-line">
            ลงชื่อ <strong>{{ $reporterSig->signer_name ?? '..............................................' }}</strong> ผู้รายงาน
        </div>
        <div>(<strong>{{ $reporterSig->signer_name ?? '..............................................' }}</strong>)</div>
        <div>ตำแหน่ง <strong>{{ $reporterSig->signer_position ?? '..............................................' }}</strong></div>
        <div>
            วันที่
            <strong>{{ $reporterSig ? $reporterSig->signed_date->format('d') : '..........' }}</strong>
            เดือน
            <strong>{{ $reporterSig ? $reporterSig->signed_date->format('m') : '..........' }}</strong>
            พ.ศ.
            <strong>{{ $reporterSig ? ($reporterSig->signed_date->year + 543) : '............' }}</strong>
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

                {{-- ลายเซ็น --}}
                <div class="signature-block" style="margin-top: 8px;">
                    @if($sig)
                        <img src="{{ public_path('storage/' . $sig->signature_image) }}" class="signature-image">
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    <div class="signature-line">
                        ลงชื่อ <strong>{{ $sig->signer_name ?? '..............................................' }}</strong>
                    </div>
                    <div>(<strong>{{ $sig->signer_name ?? '..............................................' }}</strong>)</div>
                    <div>ตำแหน่ง <strong>{{ $sig->signer_position ?? '..............................................' }}</strong></div>
                </div>
            </div>
        @endif
    @endforeach

</div>

</body>
</html>