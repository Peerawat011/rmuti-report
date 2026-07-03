<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'link_url',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // ผู้ประกาศ
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // รูปภาพประกอบ (เรียงตาม sort_order)
    public function images()
    {
        return $this->hasMany(AnnouncementImage::class)->orderBy('sort_order')->orderBy('id');
    }

    // เฉพาะประกาศที่เผยแพร่แล้ว
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // รูปปก = รูปแรก (หรือ null)
    public function getCoverImageUrlAttribute()
    {
        $first = $this->images->first();
        return $first ? $first->url : null;
    }

    // ข้อความย่อสำหรับการ์ด
    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->content), 150);
    }
}
