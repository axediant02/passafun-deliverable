<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $table = 'choices';

    protected $fillable = [
        'question_id',
        'choice_text',
        'choice_image',
        'points',
        'is_correct'
    ];

    public function quizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function participantAnswers()
    {
        return $this->hasMany(ParticipantAnswer::class);
    }
}