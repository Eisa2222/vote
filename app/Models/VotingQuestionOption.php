<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingQuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'label',
        'value',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function question()
    {
        return $this->belongsTo(VotingQuestion::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(VotingResponseAnswer::class, 'option_id');
    }
}
