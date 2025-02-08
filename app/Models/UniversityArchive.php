<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityArchive extends Model
{
    use HasFactory;

    // It's a good idea to follow Laravel's naming convention for tables (snake_case).
    protected $table = 'university_archives'; // Changed to 'university_archives'

    protected $fillable = [
        'name_en',
        'name_ar',
        'academic_id',
        'national_id',
        'phone',
        'birthdate',
        'gender',
        'city',
        'street',
        'parent_name',
        'parent_email',
        'parent_phone',
        'parent_is_abroad',
        'parent_abroad_country_id',
        'sibling_name',
        'sibling_national_id',
        'sibling_faculty',
        'sibling_phone',
        'sibling_gender',
        'has_sibling',
        'program_id',
        'score',
        'percent',
        'academic_email',
        'cert_type',
        'cert_country',
        'cert_year',
        'is_new_comer',
    ];

    protected $casts = [
        'is_new_comer' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(UserNationalLink::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function parentAbroadCountry()
    {
        return $this->belongsTo(Country::class, 'parent_abroad_country_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function userNationalLinks()
    {
        return $this->hasMany(UserNationalLink::class, 'university_Archive_id');
    }

    public static function isUniversityStudent(string $nationalId): bool
    {
        return self::where('national_id', $nationalId)->exists(); 
    }

    public static function isNationalIdRegistered(string $nationalId): bool
    {
        return UserNationalLink::where('national_id', $nationalId)->exists();
    }

    public static function findStudentByNationalId(string $nationalId) 
    {
        return self::where('national_id', $nationalId)->first(); 
    }

   
    public static function getStudentUsername(string $nationalId)
    {
        
        $universityArchive = self::where('national_id', $nationalId)->select('name_ar', 'name_en')->first();

        if (!$universityArchive) {
            return [
                'username_ar' => null,
                'username_en' => null,
            ];
        }

        // Extract usernames from the existing full names in archive
        $namePartsAr = explode(' ', $universityArchive->name_ar);
        $firstNameAr = $namePartsAr[0];
        $lastNameAr = end($namePartsAr);
        $updatedNameAr = $firstNameAr . ' ' . $lastNameAr;

        $namePartsEn = explode(' ', $universityArchive->name_en);
        $firstNameEn = $namePartsEn[0];
        $lastNameEn = end($namePartsEn);
        $updatedNameEn = $firstNameEn . ' ' . $lastNameEn;

        return [
            'username_ar' => $updatedNameAr,
            'username_en' => $updatedNameEn,
        ];
    }
}
