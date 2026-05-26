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
        'signature_image', 'signed_date',
    ];

    protected $casts = [
        'signed_date' => 'date',
    ];

    // ความสัมพันธ์: signature เป็นของ report หนึ่ง
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // ดึง URL ของรูปลายเซ็น
    public function getSignatureUrlAttribute()
    {
        return $this->signature_image ? asset('storage/' . $this->signature_image) : null;
    }
}