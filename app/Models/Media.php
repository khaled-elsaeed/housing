<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'file_name', 'mime_type', 'path', 'disk', 'file_hash', 'collection', 'size',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }
}
