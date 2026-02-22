<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Recommendation extends Model
{
    protected $fillable = [
        'assessment_id',
        'course_id',
        'rank',
        'compatibility_score',
        'llm_analysis',
        'justification',
        'strengths',
        'challenges',
    ];

    protected $casts = [
        'llm_analysis' => 'array',
        'strengths' => 'array',
        'challenges' => 'array',
        'compatibility_score' => 'decimal:2',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function roadmap(): HasOne
    {
        return $this->hasOne(Roadmap::class);
    }
}
