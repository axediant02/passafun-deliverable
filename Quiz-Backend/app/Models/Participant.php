<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $table = 'participants';

    protected $fillable = [
        'full_name',
        'email',
        'contact_number',
        'age'
    ];
    public $timestamps = true;

    public function participantQuizzes()
    {
        return $this->hasMany(ParticipantQuiz::class);
    }

    public function participantAnswers()
    {
        return $this->hasMany(ParticipantAnswer::class);
    }

    public function participantQuizSummaries()
    {
        return $this->hasMany(ParticipantQuizSummary::class);
    }
}
