<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerPath extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'average_salary_min',
        'average_salary_max',
        'market_demand',
        'key_skills',
        'growth_potential',
    ];

    protected $casts = [
        'key_skills' => 'array',
        'growth_potential' => 'array',
        'average_salary_min' => 'decimal:2',
        'average_salary_max' => 'decimal:2',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
