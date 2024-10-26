<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNationalLink extends Model
{
    use HasFactory;

    protected $table = 'user_national_link'; 

    protected $fillable = [
        'user_id',
        'university_Archive_id',
        'national_id',
    ];

    // Define the relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship to UniversityArchive
    public function universityArchive()
    {
        return $this->belongsTo(UniversityArchive::class, 'university_Archive_id');
    }

    public static function findUserByNationalID(string $nationalId): ?self
    {
        return self::where('national_id', $nationalId)->first();
    }

}
