<?php

namespace App\Repositories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Collection;

class QuizRepository
{

    public function getQuizWithBasicRelations($field, $value)
    {
        return Quiz::with([
            'quizStatus',
            'theme',
            'landingPage',
            'mechanicPage',
        ])->where($field, $value)->firstOrFail();
    }

    public function getQuizWithFullRelations($field, $value)
    {
        return Quiz::with([
            'quizStatus',
            'theme',
            'landingPage',
            'mechanicPage',
            'mechanicPage.mechanicPageInstructions.mechanicInstruction',
            'questions.choices',
            'questions.questionType',
            'getResultPage.inputForms',
            'getResultPage.jsonAnimation',
        ])->withCount('participantQuizSummaries')
        ->withCount('results')
          ->where($field, $value)
          ->firstOrFail();
    }

    public function getQuizSummaryRelations($field, $value)
    {
        return Quiz::with([
            'quizStatus',
            'participantQuizSummaries.participant',
            'results'
        ])->withCount('participantQuizSummaries')
          ->where($field, $value)
          ->firstOrFail();
    }
}

