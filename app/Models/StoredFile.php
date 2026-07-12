<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ไฟล์รูปที่เก็บ binary ลง database (แทนดิสก์ซึ่งเป็น ephemeral บน Render)
 */
class StoredFile extends Model
{
    protected $fillable = [
        'path',
        'mime',
        'data',
    ];
}
