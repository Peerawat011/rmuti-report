{{-- ฟอร์มรายงาน (ฝั่งแอดมิน) ใช้ร่วม create/edit --}}
{{-- ตัวแปร: $report (nullable), $users, $supervisors --}}

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

{{-- ===== ส่วนที่ 1: เจ้าของรายงาน (แอดมินเลือก) ===== --}}
<div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #dc3545 !important;">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;"><i class="bi bi-person-vcard-fill"></i> ๑. เจ้าของรายงาน (ผู้ไปราชการ)</strong>
        <small class="text-muted ms-2">— ข้อมูลผู้รายงานจะดึงจากผู้ใช้ที่เลือก</small>
    </div>
    <div class="card-body" style="background: #FFF8F0;">
        <label class="form-label fw-medium">เลือกผู้ใช้ <span class="text-danger">*</span></label>
        <select name="user_id" class="form-select" required>
            <option value="">-- กรุณาเลือกผู้ใช้ --</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}"
                    {{ old('user_id', $report->user_id ?? '') == $u->id ? 'selected' : '' }}>
                    {{ $u->first_name }} {{ $u->last_name }} — {{ $u->position }} ({{ $u->faculty }})
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- ===== ส่วนที่ 2: รายละเอียดการเข้ารับ ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;"><i class="bi bi-clipboard-check-fill"></i> ๒. เข้ารับการอบรม/ศึกษาดูงาน/ประชุมสัมมนา</strong>
    </div>
    <div class="card-body">

        {{-- ประเภท --}}
        <div class="mb-3">
            <label class="form-label fw-medium">ประเภทกิจกรรม <span class="text-danger">*</span></label>
            <div class="d-flex gap-3">
                @foreach(['อบรม', 'ศึกษาดูงาน', 'ประชุมสัมมนา'] as $type)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="activity_type"
                               id="type_{{ $loop->index }}" value="{{ $type }}"
                               {{ old('activity_type', $report->activity_type ?? '') == $type ? 'checked' : '' }} required>
                        <label class="form-check-label" for="type_{{ $loop->index }}">{{ $type }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- หัวข้อ --}}
        <div class="mb-3">
            <label class="form-label fw-medium">๒.๑ หัวข้อเรื่อง <span class="text-danger">*</span></label>
            <input type="text" name="topic" class="form-control"
                   value="{{ old('topic', $report->topic ?? '') }}" required>
        </div>

        {{-- เลขที่คำสั่ง + วันที่ --}}
        <div class="row g-2 mb-3">
            <div class="col-md-12">
                <label class="form-label fw-medium">๒.๒ ตามคำสั่ง/หนังสือ ที่</label>
                <input type="text" name="order_number" class="form-control"
                       value="{{ old('order_number', $report->order_number ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">ระหว่างวันที่ <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control"
                       value="{{ old('start_date', isset($report) ? $report->start_date->format('Y-m-d') : '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">ถึงวันที่ <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ old('end_date', isset($report) ? $report->end_date->format('Y-m-d') : '') }}" required>
            </div>
        </div>

        {{-- สถานที่ --}}
        <div class="mb-3">
            <label class="form-label fw-medium">๒.๓ สถานที่ <span class="text-danger">*</span></label>
            <textarea name="location" class="form-control" rows="2" required>{{ old('location', $report->location ?? '') }}</textarea>
        </div>

        {{-- หน่วยงาน --}}
        <div class="mb-3">
            <label class="form-label fw-medium">๒.๔ หน่วยงานดำเนินการ <span class="text-danger">*</span></label>
            <input type="text" name="organizer" class="form-control"
                   value="{{ old('organizer', $report->organizer ?? '') }}" required>
        </div>

        {{-- การใช้ประโยชน์ --}}
        <div class="mb-2">
            <label class="form-label fw-medium">๒.๕ การใช้ประโยชน์</label>
            @php $oldUsage = old('usage_types', $report->usage_types ?? []); @endphp
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="usage_types[]" id="usage_1"
                       value="ปฏิบัติงานในหน้าที่รับผิดชอบ" {{ in_array('ปฏิบัติงานในหน้าที่รับผิดชอบ', $oldUsage) ? 'checked' : '' }}>
                <label class="form-check-label" for="usage_1">ปฏิบัติงานในหน้าที่รับผิดชอบ</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="usage_types[]" id="usage_2"
                       value="ขยายผลแก่บุคลากรในสถานศึกษา" {{ in_array('ขยายผลแก่บุคลากรในสถานศึกษา', $oldUsage) ? 'checked' : '' }}>
                <label class="form-check-label" for="usage_2">ขยายผลแก่บุคลากรในสถานศึกษา</label>
            </div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="usage_types[]" id="usage_3"
                           value="อื่นๆ" {{ in_array('อื่นๆ', $oldUsage) ? 'checked' : '' }}>
                    <label class="form-check-label" for="usage_3">อื่นๆ ระบุ</label>
                </div>
                <input type="text" name="usage_other" class="form-control form-control-sm" style="max-width: 400px;"
                       value="{{ old('usage_other', $report->usage_other ?? '') }}" placeholder="ระบุการใช้ประโยชน์อื่นๆ">
            </div>
        </div>
    </div>
</div>

{{-- ===== ส่วนที่ 3: เอกสาร ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;"><i class="bi bi-file-earmark-text-fill"></i> ๓. เอกสาร/ตำรา/คู่มือ</strong>
    </div>
    <div class="card-body">
        <textarea name="documents" class="form-control" rows="3">{{ old('documents', $report->documents ?? '') }}</textarea>
    </div>
</div>

{{-- ===== ส่วนที่ 4: รายละเอียด ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;"><i class="bi bi-pen-fill"></i> ๔. รายละเอียดการไปศึกษา/อบรม/สัมมนา</strong>
    </div>
    <div class="card-body">
        <textarea name="details" class="form-control" rows="6">{{ old('details', $report->details ?? '') }}</textarea>
    </div>
</div>

{{-- ===== ส่วนที่ 5: ข้อเสนอแนะ ===== --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;"><i class="bi bi-lightbulb-fill"></i> ๕. สรุปข้อคิดเห็น/ข้อเสนอแนะ</strong>
    </div>
    <div class="card-body">
        <textarea name="suggestions" class="form-control" rows="5">{{ old('suggestions', $report->suggestions ?? '') }}</textarea>
    </div>
</div>

{{-- ===== ส่วนที่ 6: ผู้บังคับบัญชา ===== --}}
<div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #F57C00 !important;">
    <div class="card-header bg-white border-bottom py-2">
        <strong style="color: #EF6C00;"><i class="bi bi-people-fill"></i> ๖. ผู้บังคับบัญชา (เสนอตามลำดับชั้น)</strong>
    </div>
    <div class="card-body">
        @if($supervisors->count() < 1)
            <div class="alert alert-warning py-2 small mb-3">
                <i class="bi bi-exclamation-triangle-fill"></i> ยังไม่มีผู้ใช้ที่เป็นผู้บังคับบัญชาในระบบ — กรุณาสร้างก่อน
            </div>
        @endif

        @foreach([1 => 'ลำดับที่ ๑ (ขั้นต้น)', 2 => 'ลำดับที่ ๒ (กลาง)', 3 => 'ลำดับที่ ๓ (สูงสุด)'] as $i => $label)
            <div class="mb-3">
                <label class="form-label fw-medium">
                    {{ $label }}
                    @if($i === 1)<span class="text-danger">*</span>@else<small class="text-muted">— ไม่บังคับ</small>@endif
                </label>
                <select name="supervisor_{{ $i }}_id" class="form-select" {{ $i === 1 ? 'required' : '' }}>
                    <option value="">{{ $i === 1 ? '-- กรุณาเลือก --' : '-- ไม่เลือก --' }}</option>
                    @foreach($supervisors as $sup)
                        <option value="{{ $sup->id }}"
                            {{ old("supervisor_{$i}_id", $report->{"supervisor_{$i}_id"} ?? '') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->first_name }} {{ $sup->last_name }} — {{ $sup->position }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endforeach
    </div>
</div>
