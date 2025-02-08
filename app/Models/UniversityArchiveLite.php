<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniversityArchiveLite extends Model
{
    // Table name
    protected $table = 'nmu_archive';

    // Fillable attributes
    protected $fillable = [
        'national_id',
        'academic_id',
        'name_en',
        'name_ar',
        'academic_email',
    ];

    /**
     * Check if a given national ID belongs to a university student.
     *
     * @param string $nationalId
     * @return bool
     */
    public function isUniversityStudent(string $nationalId): bool
    {
        return $this->where('national_id', $nationalId)->exists();
    }
}
