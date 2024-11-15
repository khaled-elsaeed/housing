<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPermissionRequest extends Model
{
    protected $table = 'student_Permission_requests';  // Table name

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');  // A request belongs to a student (student model)
    }

    public function StudentPermission()
    {
        return $this->belongsTo(StudentPermission::class, 'student_Permission_id');  // A request belongs to a dorm permission
    }
}
