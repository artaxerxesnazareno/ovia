<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration_semesters',
        'shifts',
        'vacancies_per_year',
        'coordinator_name',
        'curriculum',
        'admission_requirements',
        'is_active',
    ];

    protected $casts = [
        'shifts' => 'array',
        'curriculum' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function careerPaths(): HasMany
    {
        return $this->hasMany(CareerPath::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }
}
