<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id', 'role',
        'signer_name', 'signer_position', 'comment',
        'signature_image', 'user_signature_id', 'signed_date',
    ];

    protected $casts = [
        'signed_date' => 'date',
    ];

    // ความสัมพันธ์: signature เป็นของ report หนึ่ง
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // เวอร์ชันลายเซ็นประจำตัวที่ใช้ลงนาม
    public function userSignature()
    {
        return $this->belongsTo(UserSignature::class);
    }

    // URL รูปลายเซ็น — ผ่าน route ที่ตรวจสิทธิ์ (ไม่ใช่ไฟล์สาธารณะอีกต่อไป)
    public function getSignatureUrlAttribute()
    {
        return route('signatures.image', $this);
    }
}