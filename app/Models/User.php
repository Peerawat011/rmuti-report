<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
    'first_name',
    'last_name',
    'email',
    'position',
    'department',
    'faculty',
    'role',
    'avatar',
    'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // ดึงชื่อเต็มแบบรวม
    public function getFullNameAttribute()
    {
    return $this->first_name . ' ' . $this->last_name;
    }

    // ดึง URL ของรูป avatar (หรือ null ถ้าไม่มีรูป)
public function getAvatarUrlAttribute()
{
    if ($this->avatar) {
        return asset('storage/' . $this->avatar);
    }
    return null;
}

// ดึงตัวอักษรย่อสำหรับ Avatar (ใช้ตอนไม่มีรูป)
public function getInitialsAttribute()
{
    return mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1);
}

// ตรวจว่าเป็น supervisor
public function isSupervisor()
{
    return $this->role === 'supervisor';
}

// ตรวจว่าเป็น staff
public function isStaff()
{
    return $this->role === 'staff';
}

// ตรวจว่าเป็น admin (ผู้ดูแลระบบ)
public function isAdmin()
{
    return $this->role === 'admin';
}

// ลายเซ็นประจำตัวทุกเวอร์ชัน
public function signatures()
{
    return $this->hasMany(UserSignature::class);
}

// ลายเซ็นประจำตัวเวอร์ชันล่าสุด (ที่ใช้ลงนามปัจจุบัน)
public function activeSignature()
{
    return $this->hasOne(UserSignature::class)->latestOfMany();
}

// label ภาษาไทย
public function getRoleLabelAttribute()
{
    return match($this->role) {
        'staff'      => 'บุคลากรทั่วไป',
        'supervisor' => 'ผู้บังคับบัญชา',
        'admin'      => 'ผู้ดูแลระบบ',
        default      => 'ไม่ระบุ',
    };
}

// สีของ badge
public function getRoleColorAttribute()
{
    return match($this->role) {
        'staff'      => 'secondary',
        'supervisor' => 'warning',
        'admin'      => 'danger',
        default      => 'secondary',
    };
}
}