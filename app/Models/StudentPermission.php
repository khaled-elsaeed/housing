<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPermission extends Model
{
    protected $table = 'student_Permissions';  // Table name

    public function StudentPermissionRequests()
    {
        return $this->hasMany(StudentPermissionRequest::class, 'student_Permission_id');  // A dorm permission can have many requests
    }

    
}
