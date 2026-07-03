{{-- ใช้ใน create.blade.php และ edit.blade.php --}}

@if($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i> <strong>พบข้อผิดพลาด:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ===== ส่วนที่ 1: ข้อมูลบุคลากร (Auto-fill) ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;">
            <i class="bi bi-person-vcard-fill"></i> ๑. ข้อมูลบุคลากรไปราชการ
        </strong>
        <small class="text-muted ms-2">(ดึงจากโปรไฟล์อัตโนมัติ)</small>
    </div>
    <div class="card-body" style="background: #FFF8F0;">
        <div class="row g-2">
            <div class="col-md-6">
                <small class="text-muted">ชื่อ-สกุล</small>
                <div class="form-control bg-white" style="cursor: not-allowed;">
                    {{ $user->first_name }} {{ $user->last_name }}
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-muted">ตำแหน่ง</small>
                <div class="form-control bg-white" style="cursor: not-allowed;">
                    {{ $user->position }}
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-muted">สังกัดสำนัก/สถาบัน/กอง</small>
                <div class="form-control bg-white" style="cursor: not-allowed;">
                    {{ $user->department }}
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-muted">คณะ</small>
                <div class="form-control bg-white" style="cursor: not-allowed;">
                    {{ $user->faculty }}
                </div>
            </div>
        </div>
        <small class="text-muted d-block mt-2">
            <i class="bi bi-info-circle"></i> มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน วิทยาเขตขอนแก่น
        </small>
    </div>
</div>

{{-- ===== ส่วนที่ 2: รายละเอียดการเข้ารับ ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;">
            <i class="bi bi-clipboard-check-fill"></i> ๒. เข้ารับการอบรม/ศึกษาดูงาน/ประชุมสัมมนา
        </strong>
    </div>
    <div class="card-body">

        {{-- ประเภท --}}
        <div class="mb-3">
            <label class="form-label fw-medium">ประเภทกิจกรรม <span class="text-danger">*</span></label>
            <div class="d-flex gap-3">
                @foreach(['อบรม', 'ศึกษาดูงาน', 'ประชุมสัมมนา'] as $type)
                    <div class="form-check">
                        <input class="form-check-input" type="radio"
                               name="activity_type" id="type_{{ $loop->index }}" value="{{ $type }}"
                               {{ old('activity_type', $report->activity_type ?? '') == $type ? 'checked' : '' }}
                               required>
                        <label class="form-check-label" for="type_{{ $loop->index }}">{{ $type }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- หัวข้อเรื่อง --}}
        <div class="mb-3">
            <label class="form-label fw-medium">๒.๑ หัวข้อเรื่อง <span class="text-danger">*</span></label>
            <input type="text" name="topic" class="form-control"
                   value="{{ old('topic', $report->topic ?? '') }}"
                   placeholder="เช่น อบรมการใช้งานระบบ วิชาพื้นฐานคอมพิวเตอร์" required>
        </div>

        {{-- เลขที่คำสั่ง + ระยะเวลา --}}
        <div class="row g-2 mb-3">
            <div class="col-md-12">
                <label class="form-label fw-medium">๒.๒ ตามคำสั่ง/หนังสือ ที่</label>
                <input type="text" name="order_number" class="form-control"
                       value="{{ old('order_number', $report->order_number ?? '') }}"
                       placeholder="เช่น ที่ ศธ 1234/2568">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">ระหว่างวันที่ <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control"
                       value="{{ old('start_date', isset($report) ? $report->start_date->format('Y-m-d') : '') }}"
                       required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">ถึงวันที่ <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ old('end_date', isset($report) ? $report->end_date->format('Y-m-d') : '') }}"
                       required>
            </div>
        </div>

        {{-- สถานที่ --}}
        <div class="mb-3">
            <label class="form-label fw-medium">๒.๓ สถานที่ <span class="text-danger">*</span></label>
            <textarea name="location" class="form-control" rows="2"
                      placeholder="เช่น ห้องประชุม ตึก 3 สาขา วิศวกรรมคอมพิวเตอร์" required>{{ old('location', $report->location ?? '') }}</textarea>
        </div>

        {{-- หน่วยงานดำเนินการ --}}
        <div class="mb-3">
            <label class="form-label fw-medium">๒.๔ หน่วยงานดำเนินการ <span class="text-danger">*</span></label>
            <input type="text" name="organizer" class="form-control"
                   value="{{ old('organizer', $report->organizer ?? '') }}"
                   placeholder="เช่น สาขาที่เกี่ยวข้อง" required>
        </div>

        {{-- การใช้ประโยชน์ --}}
        <div class="mb-2">
            <label class="form-label fw-medium">๒.๕ การใช้ประโยชน์</label>
            @php
                $oldUsage = old('usage_types', $report->usage_types ?? []);
            @endphp
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       name="usage_types[]" id="usage_1" value="ปฏิบัติงานในหน้าที่รับผิดชอบ"
                       {{ in_array('ปฏิบัติงานในหน้าที่รับผิดชอบ', $oldUsage) ? 'checked' : '' }}>
                <label class="form-check-label" for="usage_1">ปฏิบัติงานในหน้าที่รับผิดชอบ</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       name="usage_types[]" id="usage_2" value="ขยายผลแก่บุคลากรในสถานศึกษา"
                       {{ in_array('ขยายผลแก่บุคลากรในสถานศึกษา', $oldUsage) ? 'checked' : '' }}>
                <label class="form-check-label" for="usage_2">ขยายผลแก่บุคลากรในสถานศึกษา</label>
            </div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="usage_types[]" id="usage_3" value="อื่นๆ"
                           {{ in_array('อื่นๆ', $oldUsage) ? 'checked' : '' }}>
                    <label class="form-check-label" for="usage_3">อื่นๆ ระบุ</label>
                </div>
                <input type="text" name="usage_other" class="form-control form-control-sm" style="max-width: 400px;"
                       value="{{ old('usage_other', $report->usage_other ?? '') }}"
                       placeholder="ระบุการใช้ประโยชน์อื่นๆ">
            </div>
        </div>
    </div>
</div>

{{-- ===== ส่วนที่ 3: เอกสาร ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;">
            <i class="bi bi-file-earmark-text-fill"></i> ๓. เอกสาร/ตำรา/คู่มือ
        </strong>
    </div>
    <div class="card-body">
        <textarea name="documents" class="form-control" rows="3"
                  placeholder="ระบุเอกสาร ตำรา หรือคู่มือที่เกี่ยวข้อง">{{ old('documents', $report->documents ?? '') }}</textarea>
    </div>
</div>

{{-- ===== ส่วนที่ 4: รายละเอียด ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;">
            <i class="bi bi-pen-fill"></i> ๔. รายละเอียดการไปศึกษา ฝึกอบรม ประชุม สัมมนา
        </strong>
    </div>
    <div class="card-body">
        <small class="text-muted d-block mb-2">บรรยายสิ่งที่ได้สังเกตรู้เห็น หรือได้รับถ่ายทอดมาให้ชัดเจน</small>
        <textarea name="details" class="form-control" rows="6">{{ old('details', $report->details ?? '') }}</textarea>
    </div>
</div>

{{-- ===== ส่วนที่ 5: ข้อเสนอแนะ ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;">
            <i class="bi bi-lightbulb-fill"></i> ๕. สรุปข้อคิดเห็น/ข้อเสนอแนะ
        </strong>
    </div>
    <div class="card-body">
        <textarea name="suggestions" class="form-control" rows="5">{{ old('suggestions', $report->suggestions ?? '') }}</textarea>
    </div>
</div>

{{-- ===== ส่วนที่ 6: เลือกผู้บังคับบัญชา  ===== --}}
<div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #F57C00 !important;">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;">
            <i class="bi bi-people-fill"></i> ๖. ผู้บังคับบัญชา (เสนอตามลำดับชั้น)
        </strong>
        <small class="text-muted ms-2">— เลือกผู้ที่จะเซ็นรับรองรายงาน</small>
    </div>
    <div class="card-body">

        @if($supervisors->count() < 3)
            <div class="alert alert-warning py-2 small mb-3">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>คำเตือน:</strong> ระบบมีผู้บังคับบัญชาเพียง {{ $supervisors->count() }} คน — กรุณาแจ้งให้ผู้บังคับบัญชาคนอื่นมาสมัครก่อน
            </div>
        @endif

        {{-- ลำดับที่ 1 --}}
        <div class="mb-3">
            <label class="form-label fw-medium">
                ลำดับที่ ๑ (ขั้นต้น) <span class="text-danger">*</span>
            </label>
            <select name="supervisor_1_id" class="form-select" required>
                <option value="">-- กรุณาเลือก --</option>
                @foreach($supervisors as $sup)
                    <option value="{{ $sup->id }}"
                        {{ old('supervisor_1_id', $report->supervisor_1_id ?? '') == $sup->id ? 'selected' : '' }}>
                        {{ $sup->first_name }} {{ $sup->last_name }} — {{ $sup->position }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ลำดับที่ 2 --}}
        <div class="mb-3">
            <label class="form-label fw-medium">
                ลำดับที่ ๒ (กลาง) <small class="text-muted">— ไม่บังคับ</small>
            </label>
            <select name="supervisor_2_id" class="form-select">
                <option value="">-- ไม่เลือก --</option>
                @foreach($supervisors as $sup)
                    <option value="{{ $sup->id }}"
                        {{ old('supervisor_2_id', $report->supervisor_2_id ?? '') == $sup->id ? 'selected' : '' }}>
                        {{ $sup->first_name }} {{ $sup->last_name }} — {{ $sup->position }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ลำดับที่ 3 --}}
        <div class="mb-3">
            <label class="form-label fw-medium">
                ลำดับที่ ๓ (สูงสุด) <small class="text-muted">— ไม่บังคับ</small>
            </label>
            <select name="supervisor_3_id" class="form-select">
                <option value="">-- ไม่เลือก --</option>
                @foreach($supervisors as $sup)
                    <option value="{{ $sup->id }}"
                        {{ old('supervisor_3_id', $report->supervisor_3_id ?? '') == $sup->id ? 'selected' : '' }}>
                        {{ $sup->first_name }} {{ $sup->last_name }} — {{ $sup->position }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="alert" style="background: #FFF3E0; border: 1px solid #FFB74D; color: #BF360C;">
            <small>
                <i class="bi bi-info-circle-fill"></i>
                <strong>หมายเหตุ:</strong>
                ต้องเลือกผู้บังคับบัญชาอย่างน้อย 1 คน (ลำดับที่ ๑)
                — สามารถเลือกลำดับที่ ๒ และ ๓ เพิ่มได้ตามต้องการ
            </small>
        </div>
    </div>
</div>

{{-- ปุ่ม --}}
<div class="d-flex gap-2 justify-content-end mb-4">
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle"></i> ยกเลิก
    </a>
    <button type="submit" class="btn btn-gov">
        <i class="bi bi-check2-circle"></i>
        {{ isset($report) ? 'บันทึกการแก้ไข' : 'บันทึกรายงาน' }}
    </button>
</div>