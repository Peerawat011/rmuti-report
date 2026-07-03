<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AnnouncementImage extends Model
{
    protected $fillable = [
        'announcement_id',
        'path',
        'sort_order',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    // URL เต็มของรูป
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    // ลบไฟล์จริงตอนลบ record
    protected static function booted()
    {
        static::deleting(function (AnnouncementImage $image) {
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }
}
