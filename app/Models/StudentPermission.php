<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPermission extends Model
{
    protected $table = 'student_Permissions';  // Table name

  

    protected $fillable = [
        'name','category'
    ];


    
}
