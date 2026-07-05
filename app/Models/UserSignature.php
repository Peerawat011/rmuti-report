<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSignature extends Model
{
    protected $fillable = [
        'user_id',
        'path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // หมายเหตุ: ไม่ลบไฟล์ตอนลบ record — เอกสารเก่ายังอ้างอิงเวอร์ชันนี้อยู่
}
