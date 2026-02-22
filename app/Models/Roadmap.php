<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Roadmap extends Model
{
    protected $fillable = [
        'recommendation_id',
        'short_term_goals',
        'medium_term_goals',
        'long_term_goals',
        'resources',
        'certifications',
        'books',
        'communities',
        'progress',
    ];

    protected $casts = [
        'short_term_goals' => 'array',
        'medium_term_goals' => 'array',
        'long_term_goals' => 'array',
        'resources' => 'array',
        'certifications' => 'array',
        'books' => 'array',
        'communities' => 'array',
        'progress' => 'array',
    ];

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }
}
