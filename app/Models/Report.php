<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supervisor_1_id', 'supervisor_2_id', 'supervisor_3_id',
        'reporter_name', 'reporter_position', 'reporter_department', 'reporter_faculty',
        'activity_type', 'topic', 'order_number',
        'start_date', 'end_date', 'total_days',
        'location', 'organizer',
        'usage_types', 'usage_other',
        'documents', 'details', 'suggestions',
        'status',
    ];

    protected $casts = [
        'usage_types' => 'array',          // แปลง JSON ↔ array อัตโนมัติ
        'start_date'  => 'date',
        'end_date'    => 'date',
    ];

    // ความสัมพันธ์: report เป็นของ user คนหนึ่ง
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ความสัมพันธ์: report มีหลาย signatures
    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    // ความสัมพันธ์: supervisor แต่ละลำดับ
    public function supervisor1()
    {
        return $this->belongsTo(User::class, 'supervisor_1_id');
    }

    public function supervisor2()
    {
        return $this->belongsTo(User::class, 'supervisor_2_id');
    }

    public function supervisor3()
    {
        return $this->belongsTo(User::class, 'supervisor_3_id');
    }

    // ตรวจว่า user เป็น supervisor ของ report นี้ลำดับไหน (1, 2, 3, หรือ null)
    public function getSupervisorRoleFor($userId)
    {
        if ($this->supervisor_1_id === $userId) return 1;
        if ($this->supervisor_2_id === $userId) return 2;
        if ($this->supervisor_3_id === $userId) return 3;
        return null;
    }

    // ตรวจว่า supervisor ลำดับนี้เซ็นแล้วหรือยัง
    public function hasSignatureFromRole($role)
    {
        return $this->signatures()->where('role', $role)->exists();
    }

    /**
 * ตรวจว่า user คนนี้สามารถเซ็นได้ตอนนี้หรือยัง
 * Workflow: reporter → supervisor_1 → supervisor_2 → supervisor_3
 */
    public function canSign($userId)
    {
        $supervisorRole = $this->getSupervisorRoleFor($userId);
        if (!$supervisorRole) return false;

        // ผู้รายงานต้องเซ็นก่อนเสมอ
        if (!$this->hasSignatureFromRole('reporter')) return false;

        // เซ็นซ้ำไม่ได้
        if ($this->hasSignatureFromRole('supervisor_' . $supervisorRole)) return false;

        // ต้องรอลำดับก่อนหน้าเซ็น (เฉพาะถ้ามีลำดับก่อนหน้า)
        if ($supervisorRole >= 2 && $this->supervisor_1_id && !$this->hasSignatureFromRole('supervisor_1')) {
            return false;
        }
        if ($supervisorRole >= 3 && $this->supervisor_2_id && !$this->hasSignatureFromRole('supervisor_2')) {
            return false;
        }

        return true;
    }

/**
 * อธิบายว่าทำไมเซ็นไม่ได้ (สำหรับแสดงข้อความ)
 */
    public function whyCantSign($userId)
    {
        $role = $this->getSupervisorRoleFor($userId);
        if (!$role) return 'คุณไม่ได้เป็นผู้บังคับบัญชาของรายงานนี้';

        if (!$this->hasSignatureFromRole('reporter')) {
            return 'ผู้รายงานยังไม่ได้ลงนาม กรุณารอจนกว่าผู้รายงานจะลงนามก่อน';
        }

        if ($this->hasSignatureFromRole('supervisor_' . $role)) {
            return 'คุณได้ลงนามรายงานนี้แล้ว';
        }

        if ($role >= 2 && !$this->hasSignatureFromRole('supervisor_1')) {
            return 'รอผู้บังคับบัญชาลำดับที่ ๑ ลงนามก่อน';
        }
        if ($role >= 3 && !$this->hasSignatureFromRole('supervisor_2')) {
            return 'รอผู้บังคับบัญชาลำดับที่ ๒ ลงนามก่อน';
        }

        return 'ไม่สามารถลงนามได้';
    }

/**
 * คำนวณความคืบหน้า (%) ของการเซ็น
 */
    public function getSigningProgress()
    {
        // reporter (1) + supervisors ที่เลือกจริง
        $totalSupervisors = collect([
            $this->supervisor_1_id,
            $this->supervisor_2_id,
            $this->supervisor_3_id,
        ])->filter()->count();

        $total = 1 + $totalSupervisors;
        $signed = $this->signatures()->count();

        return $total > 0 ? round(($signed / $total) * 100) : 0;
    }

    // คืนค่าจำนวนคนที่ต้องเซ็นทั้งหมด (ใช้ใน badge)
    public function getTotalRequiredSignatures()
    {
        $supervisors = collect([
            $this->supervisor_1_id,
            $this->supervisor_2_id,
            $this->supervisor_3_id,
        ])->filter()->count();

        return 1 + $supervisors;
    }

    // ดึงลายเซ็นของผู้รายงาน
    public function reporterSignature()
    {
        return $this->hasOne(Signature::class)->where('role', 'reporter');
    }

    // ดึง label สถานะภาษาไทย
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'ร่าง',
            'pending_signature' => 'รอลงนาม',
            'signed' => 'ลงนามแล้ว',
            'approved' => 'อนุมัติแล้ว',
            default => 'ไม่ทราบสถานะ',
        };
    }

    // ดึงสีของ badge สถานะ
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'pending_signature' => 'warning',
            'signed' => 'info',
            'approved' => 'success',
            default => 'secondary',
        };
    }
}