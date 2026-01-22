<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'category',
        'question_text',
        'question_type',
        'weight',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight' => 'integer',
        'order' => 'integer',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(AssessmentResponse::class);
    }
}
