<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestionLink extends Model
{
    use HasFactory;

    protected $table = 'quiz_question_links';

    protected $fillable = ['quiz_id', 'question_id'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function quizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
