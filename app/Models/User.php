<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'email',
        'username_ar',
        'username_en',
        'password',
        'is_active',
        'profile_picture',
        'last_login',
        'is_verified',
        'activation_token',
        'activation_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activation_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime', 
        'is_active' => 'boolean', 
        'is_verified' => 'boolean', 
        'profile_picture' => 'string', 
    ];

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function nationalLink()
    {
        return $this->hasOne(UserNationalLink::class);
    }

    public function universityArchive()
    {
        return $this->hasOneThrough(UniversityArchive::class, UserNationalLink::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isResident(): bool
    {
        return $this->hasRole('resident');
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    public function isDeleted(): bool
    {
        return !is_null($this->deleted_at);
    }

    public function hasStudentProfile(): bool
    {
        return $this->student()->exists();
    }

    public function allowLateProfileCompletion(): bool
    {
        return optional($this->student)->can_complete_late ?? false;
    }

    public static function findUserByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    public function isNewComerStudent(User $user): bool
    {
        return $user->student->universityArchive->is_new_comer ?? false;
    }

    public function isOldStudent(User $user): bool
    {
        return !$this->isNewComerStudent($user);
    }
    
}
