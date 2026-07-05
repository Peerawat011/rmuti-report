@extends('layouts.app')

@section('title', 'รายงาน #' . $report->id)

@section('content')
<div class="container py-4" style="max-width: 900px;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- แถบแจ้งถูกส่งกลับแก้ไข --}}
    @if($report->status === 'revision')
        <div class="alert alert-danger">
            <div class="fw-bold mb-1">
                <i class="bi bi-arrow-return-left"></i> รายงานถูกส่งกลับให้แก้ไข
            </div>
            <div class="mb-1">
                <strong>เหตุผล:</strong> {{ $report->revision_reason }}
            </div>
            <div class="small">
                โดย {{ $report->revisionBy->full_name ?? 'ผู้บังคับบัญชา' }}
                — {{ optional($report->revision_at)->format('d/m/Y H:i') }}
                @if($report->user_id === Auth::id())
                    <br><i class="bi bi-info-circle"></i> กรุณากดปุ่ม "แก้ไข" ปรับปรุงรายงานตามเหตุผลข้างต้น แล้วลงนามใหม่อีกครั้ง
                @endif
            </div>
        </div>
    @endif

    <nav class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="link-gov">รายงานของฉัน</a></li>
            <li class="breadcrumb-item active">รายงาน #{{ $report->id }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h3 class="mb-1" style="color: #EF6C00;">
                <i class="bi bi-file-earmark-text-fill"></i>
                รายงาน {{ $report->doc_number ?? '#' . str_pad($report->id, 4, '0', STR_PAD_LEFT) }}
            </h3>
            <span class="badge bg-{{ $report->status_color }}">{{ $report->status_label }}</span>
            <small class="text-muted ms-2">สร้างเมื่อ {{ $report->created_at->format('d/m/Y H:i') }}</small>
        </div>
        <div class="d-flex gap-2">
            
            {{-- ===== ปุ่ม Download PDF (ทุกคนที่เข้าได้) ===== --}}
            <a href="{{ route('reports.pdf', $report) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="bi bi-file-earmark-pdf-fill"></i> ดาวน์โหลด PDF
            </a>
            
            @if($report->user_id === Auth::id())
                <a href="{{ route('reports.edit', $report) }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-pencil"></i> แก้ไข
                </a>

                @php $reporterSig = $report->reporterSignature; @endphp
                @if($reporterSig)
                    <a href="{{ route('reports.sign', $report) }}" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-pen"></i> แก้ไขลายเซ็น
                    </a>
                @else
                    <a href="{{ route('reports.sign', $report) }}" class="btn btn-gov btn-sm">
                        <i class="bi bi-pen"></i> ลงนาม
                    </a>
                @endif
            @endif

            {{-- ถ้าเป็น supervisor ของรายงานนี้ → แสดงปุ่ม "ลงนามอนุมัติ" (Phase 3C) --}}
            @php $myRole = $report->getSupervisorRoleFor(Auth::id()); @endphp
@if($myRole)
    <span class="badge bg-info align-self-center me-2">
        คุณเป็นผู้บังคับบัญชา ลำดับที่ {{ $myRole }}
    </span>

    @if($report->canSign(Auth::id()))
        <a href="{{ route('supervisor.sign', $report) }}" class="btn btn-gov btn-sm">
            <i class="bi bi-pen-fill"></i> ลงนามอนุมัติ
        </a>
            @elseif($report->hasSignatureFromRole('supervisor_' . $myRole))
                <span class="badge bg-success align-self-center">
                    <i class="bi bi-check-circle-fill"></i> คุณลงนามแล้ว
                </span>
            @else
                <button class="btn btn-secondary btn-sm" disabled
                        title="{{ $report->whyCantSign(Auth::id()) }}">
                    <i class="bi bi-clock"></i> รอลำดับก่อนหน้า
                </button>
            @endif
        @endif
    </div>
    </div>

    {{-- ส่วนที่ 1: ข้อมูลบุคลากร --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-2">
            <strong style="color: #EF6C00;">๑. ข้อมูลบุคลากร</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted">ชื่อ-สกุล</small>
                    <div><strong>{{ $report->reporter_name }}</strong></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">ตำแหน่ง</small>
                    <div><strong>{{ $report->reporter_position }}</strong></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">สังกัด</small>
                    <div><strong>{{ $report->reporter_department }}</strong></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">คณะ</small>
                    <div><strong>{{ $report->reporter_faculty }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ส่วนที่ 2 --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-2">
            <strong style="color: #EF6C00;">๒. รายละเอียดการเข้ารับ</strong>
        </div>
        <div class="card-body">
            <p><strong>ประเภท:</strong>
                <span class="badge" style="background: #FFB74D; color: #4E342E;">{{ $report->activity_type }}</span>
            </p>
            <p><strong>๒.๑ หัวข้อเรื่อง:</strong> {{ $report->topic }}</p>
            @if($report->order_number)
                <p><strong>๒.๒ ตามคำสั่ง:</strong> {{ $report->order_number }}</p>
            @endif
            <p>
                <strong>ระยะเวลา:</strong>
                {{ $report->start_date->format('d/m/Y') }} - {{ $report->end_date->format('d/m/Y') }}
                <span class="text-muted">(รวม {{ $report->total_days }} วัน)</span>
            </p>
            <p><strong>๒.๓ สถานที่:</strong> {{ $report->location }}</p>
            <p><strong>๒.๔ หน่วยงานดำเนินการ:</strong> {{ $report->organizer }}</p>
            <p class="mb-1"><strong>๒.๕ การใช้ประโยชน์:</strong></p>
            <ul class="mb-0">
                @foreach($report->usage_types ?? [] as $usage)
                    <li>{{ $usage }}@if($usage == 'อื่นๆ' && $report->usage_other): {{ $report->usage_other }}@endif</li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- ===== ผู้บังคับบัญชา ===== --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-2">
            <strong style="color: #EF6C00;">
                <i class="bi bi-people-fill"></i> ผู้บังคับบัญชา (เสนอตามลำดับชั้น)
            </strong>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach([1, 2, 3] as $level)
                    @php
                        $sup = $report->{"supervisor{$level}"};
                    @endphp
                    @if($sup)
                        <div class="col-md-4">
                            <div class="border rounded p-2" style="background: #FFF8F0;">
                                <small class="text-muted">ลำดับที่ {{ $level }}</small>
                                <div><strong>{{ $sup->first_name }} {{ $sup->last_name }}</strong></div>
                                <small class="text-muted">{{ $sup->position }}</small>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ส่วนที่ 3-5 --}}
    @if($report->documents)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-2">
                <strong style="color: #EF6C00;">๓. เอกสาร/ตำรา/คู่มือ</strong>
            </div>
            <div class="card-body" style="white-space: pre-wrap;">{{ $report->documents }}</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-2">
            <strong style="color: #EF6C00;">๔. รายละเอียด</strong>
        </div>
        <div class="card-body" style="white-space: pre-wrap;">{{ $report->details }}</div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white py-2">
            <strong style="color: #EF6C00;">๕. ข้อคิดเห็น/ข้อเสนอแนะ</strong>
        </div>
        <div class="card-body" style="white-space: pre-wrap;">{{ $report->suggestions }}</div>
    </div>

    {{-- ===== หน้าที่ 2: ลายเซ็นทั้งหมด ===== --}}
<div class="card border-0 shadow-sm mb-3" style="border-top: 4px solid #F57C00 !important;">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <strong style="color: #EF6C00;">
                <i class="bi bi-signature"></i> ส่วนลงนาม (หน้าที่ 2)
            </strong>
            <div>
                <small class="text-muted me-2">ความคืบหน้า:</small>
                <span class="badge bg-{{ $report->getSigningProgress() == 100 ? 'success' : 'warning text-dark' }}">
                    {{ $report->signatures()->count() }} / {{ $report->getTotalRequiredSignatures() }} คน
                </span>
            </div>
        </div>
        <small class="text-muted d-block mt-1">ขอรับรองว่าข้อมูลที่รายงานข้างต้นเป็นจริงทุกประการ</small>
    </div>
    <div class="card-body">

        {{-- ===== ลายเซ็นผู้รายงาน ===== --}}
        @php $reporterSig = $report->reporterSignature; @endphp
        <div class="mb-3 p-3" style="background: #FFF8F0; border-radius: 8px; border-left: 4px solid #F57C00;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong style="color: #BF360C;">
                    <i class="bi bi-person-fill"></i> ผู้รายงาน
                </strong>
                <span class="d-flex align-items-center gap-2">
                    @if($reporterSig)
                        <span class="badge bg-success"><i class="bi bi-check"></i> ลงนามแล้ว</span>
                        @if(Auth::user()->isAdmin())
                            <form action="{{ route('reports.sign.delete', [$report, $reporterSig]) }}" method="POST" class="d-inline m-0"
                                  onsubmit="return confirm('ยืนยันลบลายเซ็นผู้รายงาน?\n\nสถานะรายงานจะกลับเป็น \'ร่าง\' และผู้รายงานต้องลงนามใหม่');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="ลบลายเซ็น (ผู้ดูแลระบบ)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="badge bg-secondary">รอลงนาม</span>
                    @endif
                </span>
            </div>

            @if($reporterSig)
                <div class="row align-items-center">
                    <div class="col-md-5 text-center">
                        <div style="background: white; padding: 10px; border-radius: 6px; display: inline-block; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                            <img src="{{ $reporterSig->signature_url }}"
                                 style="max-height: 70px; max-width: 180px;">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div>ลงชื่อ <strong>{{ $reporterSig->signer_name }}</strong></div>
                        <small class="text-muted">ตำแหน่ง: {{ $reporterSig->signer_position }}</small><br>
                        <small class="text-muted">วันที่: {{ $reporterSig->signed_date->format('d/m/Y') }}</small>
                    </div>
                </div>
            @endif
        </div>

        {{-- ===== ลายเซ็นผู้บังคับบัญชา (แสดงเฉพาะลำดับที่เลือก) ===== --}}
        @foreach([1, 2, 3] as $level)
            @php
                $sig = $report->signatures()->where('role', 'supervisor_' . $level)->first();
                $sup = $report->{"supervisor{$level}"};
            @endphp
            @if($sup)
                <div class="mb-3 p-3" style="background: {{ $sig ? '#F1F8F4' : '#F8F8F8' }}; border-radius: 8px; border-left: 4px solid {{ $sig ? '#28a745' : '#ccc' }};">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong style="color: {{ $sig ? '#1e7e34' : '#888' }};">
                            <i class="bi bi-person-check-fill"></i> ผู้บังคับบัญชา ลำดับที่ {{ $level }}
                            <small class="text-muted">— {{ $sup->first_name }} {{ $sup->last_name }}</small>
                        </strong>
                        <span class="d-flex align-items-center gap-2">
                            @if($sig)
                                <span class="badge bg-success"><i class="bi bi-check"></i> ลงนามแล้ว</span>
                                @if(Auth::user()->isAdmin())
                                    <form action="{{ route('reports.sign.delete', [$report, $sig]) }}" method="POST" class="d-inline m-0"
                                          onsubmit="return confirm('ยืนยันลบลายเซ็นผู้บังคับบัญชา ลำดับที่ {{ $level }}?\n\nหากรายงานอนุมัติแล้ว สถานะจะถอยกลับเป็น \'ลงนามแล้ว\' และต้องลงนามใหม่');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="ลบลายเซ็น (ผู้ดูแลระบบ)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="badge bg-secondary">รอลงนาม</span>
                            @endif
                        </span>
                    </div>

                    @if($sig)
                        <div class="mb-2 p-2" style="background: white; border-radius: 6px; font-size: 0.9rem;">
                            <small class="text-muted d-block">ความเห็น:</small>
                            @if($sig->comment)
                                {{ $sig->comment }}
                            @else
                                <span class="text-muted fst-italic">— ไม่มีความเห็นเพิ่มเติม —</span>
                            @endif
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-5 text-center">
                                <div style="background: white; padding: 10px; border-radius: 6px; display: inline-block; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                                    <img src="{{ $sig->signature_url }}"
                                        style="max-height: 70px; max-width: 180px;">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div>ลงชื่อ <strong>{{ $sig->signer_name }}</strong></div>
                                <small class="text-muted">ตำแหน่ง: {{ $sig->signer_position }}</small><br>
                                <small class="text-muted">วันที่: {{ $sig->signed_date->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach

        {{-- ปุ่มลงนาม (เฉพาะเจ้าของรายงาน) --}}
        @if($report->user_id === Auth::id() && !$reporterSig)
            <div class="text-center mt-3">
                <a href="{{ route('reports.sign', $report) }}" class="btn btn-gov">
                    <i class="bi bi-pen-fill"></i> ลงนามผู้รายงาน
                </a>
            </div>
        @endif

    </div>
</div>
    <div class="card-body">

        @if($reporterSig)
            {{-- มีลายเซ็นแล้ว --}}
            <div class="text-center p-3" style="background: #FFF8F0; border-radius: 8px;">
                <div style="background: white; padding: 12px; border-radius: 8px; display: inline-block; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
                    <img src="{{ $reporterSig->signature_url }}"
                         alt="ลายเซ็น"
                         style="max-height: 100px; max-width: 250px;">
                </div>
                <div class="mt-3">
                    <div>ลงชื่อ <strong>{{ $reporterSig->signer_name }}</strong> ผู้รายงาน</div>
                    <div class="text-muted small">ตำแหน่ง: {{ $reporterSig->signer_position }}</div>
                    <div class="text-muted small">
                        วันที่ลงนาม: {{ $reporterSig->signed_date->format('d/m/Y') }}
                    </div>
                </div>

                @php $isOwner = $report->user_id === Auth::id(); @endphp
                @if($isOwner || Auth::user()->isAdmin())
                    <hr class="my-3" style="max-width: 300px; margin-left: auto; margin-right: auto;">

                    <div class="d-flex gap-2 justify-content-center">
                        @if($isOwner)
                            <a href="{{ route('reports.sign', $report) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i> แก้ไขลายเซ็น
                            </a>
                        @endif
                        <form action="{{ route('reports.sign.delete', [$report, $reporterSig]) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('ยืนยันการลบลายเซ็น?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> ลบลายเซ็น
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @else
            {{-- ยังไม่ลงนาม --}}
            <div class="text-center p-4" style="background: #FFF8F0; border-radius: 8px; border: 2px dashed #FFB74D;">
                <i class="bi bi-pen" style="font-size: 3rem; color: #FFB74D;"></i>
                <h6 class="mt-3 mb-1" style="color: #BF360C;">ยังไม่ได้ลงนาม</h6>
                <p class="text-muted small mb-3">กรุณาลงนามเพื่อรับรองความถูกต้องของรายงาน</p>
                <a href="{{ route('reports.sign', $report) }}" class="btn btn-gov">
                    <i class="bi bi-pen-fill"></i> ลงนามตอนนี้
                </a>
            </div>
        @endif

    </div>
</div>

</div>
@endsection