<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantQuizSummary extends Model
{
    use HasFactory;

    protected $table = 'participant_quiz_summaries';

    protected $fillable = [
        'participant_id',
        'quiz_id',
        'score',
        'result_id',
        'unique_result_id',
        'completed_at',
        'image_thumbnail'
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

    public function result()
    {
        return $this->belongsTo(Result::class, 'result_id');
    }

    public function participantAnswers()
    {
        return $this->hasMany(ParticipantAnswer::class, 'quiz_id', 'quiz_id');
    }
}
