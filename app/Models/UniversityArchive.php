<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityArchive extends Model
{
    use HasFactory;

        protected $table = 'university_Archive';

        protected $fillable = [
        'name_en',
        'name_ar',
        'national_id',
        'mobile',
        'birthdate',
        'gender',
        'city',
        'street',
        'parent_name',
        'parent_email',
        'parent_mobile',
        'parent_is_abroad',
        'parent_abroad_country_id',
        'sibling_name',
        'sibling_national_id',
        'sibling_faculty',
        'sibling_mobile',
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

    
}
