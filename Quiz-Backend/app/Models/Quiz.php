<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Quiz extends Model
{
    use HasFactory, Searchable;

    protected $table = 'quizzes';

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'cover_image',
        'quiz_status_id',
        'is_featured',
        'theme_id',
        'landing_page_id',
        'mechanic_page_id',
        'admin_id',
        'uid',
        'share_thumbnail_image',
        'participant_id',
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    public function quizStatus()
    {
        return $this->belongsTo(QuizStatus::class, 'quiz_status_id');
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }

    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }

    public function mechanicPage()
    {
        return $this->belongsTo(MechanicPage::class, 'mechanic_page_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function participantQuizzes()
    {
        return $this->hasMany(ParticipantQuiz::class);
    }

    public function participantQuizSummaries()
    {
        return $this->hasMany(ParticipantQuizSummary::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'quiz_id');
    }

    public function thumbnailCustomization()
    {
        return $this->has(Result::class, 'quiz_id');
    }

    public function getResultPage()
    {
        return $this->hasMany(GetResultPage::class);
    }
    public function inputForms()
    {
        return $this->hasMany(InputForm::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'participant_quizzes');
    }
}