<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingResponseAnswer extends Model
{
    protected $fillable = [
        'response_id',
        'question_id',
        'option_id',
        'text_answer',
    ];

    public function response()
    {
        return $this->belongsTo(VotingResponse::class, 'response_id');
    }

    public function question()
    {
        return $this->belongsTo(VotingQuestion::class, 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(VotingQuestionOption::class, 'option_id');
    }
}
