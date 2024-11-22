<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

   protected $fillable = ['user_id', 'file_path', 'status']; // Include 'status'

   protected $casts = [
       'status' => 'string', 
   ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
