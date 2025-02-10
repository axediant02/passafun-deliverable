<?php

namespace App\Http\Services;

use App\Models\Quiz;
use App\Models\QuizQuestion;

class QuestionDuplicatorService
{
    public function getQuestionWithChoicesAndQuestionType($questionId)
    {
        return QuizQuestion::with('choices', 'questionType')->findOrFail($questionId);
    }

    public function incrementQuestionOrder($originalQuestion)
    {
        QuizQuestion::where('quiz_id', $originalQuestion->quiz_id)
            ->where('question_order', '>', $originalQuestion->question_order)
            ->increment('question_order');
    }

    public function duplicateQuestion($originalQuestion)
    {
        $duplicateQuestion = $originalQuestion->replicate(['question_order']);
        $duplicateQuestion->question_order = $originalQuestion->question_order + 1;
        $duplicateQuestion->save();
        return $duplicateQuestion;
    }

    public function duplicateChoices($originalQuestion, $duplicateQuestion)
    {
        foreach ($originalQuestion->choices as $choice) {
            $this->duplicateChoice($choice, $duplicateQuestion->id);
        }
    }

    public function duplicateChoice($choice, $duplicateQuestionId)
    {
        $choice->replicate(['question_id'])->fill(['question_id' => $duplicateQuestionId])->save();
    }

    public function getQuizQuestions($quizId)
    {
        $quiz = Quiz::with(['questions.choices', 'questions.questionType'])->findOrFail($quizId);
        return response()->json($quiz->questions);
    }
}
