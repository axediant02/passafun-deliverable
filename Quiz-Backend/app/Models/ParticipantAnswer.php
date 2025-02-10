<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantAnswer extends Model
{
    use HasFactory;

    protected $table = 'participant_answers';

    protected $fillable = [
        'participant_id',
        'summary_id',
        'quiz_id',
        'question_id',
        'choice_id',
        'answer_text',
    ];
    public $timestamps = false;
    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function quizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function choice()
    {
        return $this->belongsTo(Choice::class, 'choice_id');
    }

    public function participantQuizSummaries()
    {
        return $this->belongsTo(ParticipantQuizSummary::class, 'participant_quiz_summary_id');
    }
}
