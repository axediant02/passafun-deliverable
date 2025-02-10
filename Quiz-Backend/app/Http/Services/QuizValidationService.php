<?php

namespace App\Http\Services;

use App\Enums\QuestionTypeEnums;

class QuizValidationService
{

    private array $errors = [];

    public function validateQuizCompleteness($quiz)
    {

        $this->errors = [];

        $this->validateBasicQuizInfo($quiz);
        $this->validateLandingPage($quiz);
        $this->validateQuestions($quiz);
        $this->validateSubmissionInfo($quiz);
        $this->validateResults($quiz);
        

        return [
            'isValid' => empty($this->errors),
            'errors' => $this->errors,
        ];
    }

    private function addError(string $error): void 
    {
        if (!in_array($error, $this->errors)) {
            $this->errors[] = $error;
        }
    }

    private function validateBasicQuizInfo($quiz): void
    {

        if ($this->isEmptyField($quiz['name'])) {
            $this->addError('Quiz Name');
        }

        if ($this->isEmptyField($quiz['description'])) {
            $this->addError('Quiz Description');
        }

        if ($this->isEmptyField($quiz['thumbnail'])) {
            $this->addError('Quiz Thumbnail');
        }
        if ($this->isEmptyField($quiz['cover_image'])) {
            $this->addError('Quiz Cover Image');
        }
    }

    private function validateLandingPage($quiz): void
    {
        if (!$quiz->landingPage) {
            $this->addError('Landing page is missing');
            return;
        }

        $landingPage = $quiz->landingPage;

        if ($this->isEmptyField($landingPage->sub_header)) {
            $this->addError('Page Subtitle');
        }
    }

    private function validateQuestions($quiz): void
    {
        $minimumQuestions = 5;

        if ($quiz->questions->count() < $minimumQuestions) {
            $this->addError("Quiz must have at least {$minimumQuestions} questions");
        }

        $this->validateQuestionPoints($quiz->questions);
    }

    private function validateQuestionPoints($questions): void
    {
        foreach ($questions as $question) {
            if ($question->question_type_id === QuestionTypeEnums::OpenEnded) {
                continue;
            }

            if (!$this->hasValidPoints($question->choices)) {
                $this->addError('Questions must have at least one choice with points greater than 0');
                break; 
            }
        }
    }

    private function hasValidPoints($choices): bool
    {
        return $choices->contains(function ($choice) {
            return $choice->points > 0;
        });
    }

    private function validateSubmissionInfo($quiz): void
    {
        if (!$quiz->getResultPage || $quiz->getResultPage->isEmpty()) {
            $this->addError('Submission page is missing');
            return;
        }

        $submissionInfo = $quiz->getResultPage->first();

        if ($this->isEmptyField($submissionInfo->header)) {
            $this->addError('Submission Page Title');
        }

        if ($this->isEmptyField($submissionInfo->button_text)) {
           $this->addError('Submission Page CTA Button Text');
        }
        if ($this->isEmptyField($submissionInfo->image_type_id)) {
           $this->addError('Submission Page Thumbnail');
        }

    }

    private function validateResults($quiz): void
    {
        if (!$quiz->results || $quiz->results->isEmpty()) {
            $this->addError('Quiz results are missing');
            return;
        }

        if ($quiz->questions->isEmpty()) {
            $this->addError('Questions must exist before validating results');
            return;
        }

        $perfectScore = $this->calculatePerfectScore($quiz->questions);
        $sortedResults = $quiz->results->sortBy('min_points');

        $this->validateResultContent($sortedResults);
        $this->validateResultRanges($sortedResults, $perfectScore);
    }

    private function validateResultContent($results): void
    {
        foreach ($results as $result) {
            if ($this->isEmptyField($result->header)) {
                $this->addError('Result name is required');
            }
            if ($this->isEmptyField($result->description)) {
                $this->addError('Result description is required');
            }
            if ($result->min_points < 0) {
                $this->addError('Minimum points cannot be negative');
            }
        }
    }

    private function validateResultRanges($sortedResults, $perfectScore): void
    {
        if ($sortedResults->count() === 1) {
            $this->validateSingleResult($sortedResults->first(), $perfectScore);
            return;
        }

        $this->validateMultipleResults($sortedResults, $perfectScore);
    }

    private function validateMultipleResults($sortedResults, $perfectScore): void
    {
        $previousMax = -1;
        $hasOverlap = false;

        foreach ($sortedResults as $result) {
            $this->checkOverlap($result, $previousMax, $hasOverlap);
            $this->checkGaps($result, $previousMax);
            $previousMax = $result->max_points;
        }

        $this->validateResultBoundaries($sortedResults, $perfectScore);
    }

    private function checkOverlap($result, $previousMax, &$hasOverlap): void
    {
        if ($result->min_points <= $previousMax && !$hasOverlap) {
            $this->addError('Overlapping score ranges detected');
            $hasOverlap = true;
        }
    }

    private function checkGaps($result, $previousMax): void
    {
        if ($result->min_points > ($previousMax + 1)) {
            $this->addError("Gap detected in score ranges (missing scores between {$previousMax} and {$result->min_points})");
        }
    }

    private function validateResultBoundaries($sortedResults, $perfectScore): void
    {
        $firstResult = $sortedResults->first();
        $lastResult = $sortedResults->last();

        if ($firstResult->min_points !== 0 && $firstResult->min_points !== 1) {
            $this->addError('First result must start from score 0 or 1');
        }
        if ($lastResult->max_points !== $perfectScore) {
            $this->addError("Results must cover scores from 0 up to {$perfectScore}");
        }
    }

    private function validateSingleResult($result, $perfectScore): void
    {

        if ($result->min_points !== 0 && $result->min_points !== 1) {
            $this->addError('Single result must start from score 0 or 1.');
        }
        if ($result->max_points !== $perfectScore) {
            $this->addError("Single result must cover all scores (0 to {$perfectScore}).");
        }

    }

    private function calculatePerfectScore($questions): int
    {
        $totalPoints = 0;

        foreach ($questions as $question) {
            if ($question->question_type_id === QuestionTypeEnums::OpenEnded) {
                continue;
            }

            if ($question->question_type_id === QuestionTypeEnums::MultipleSelect) {
                $totalPoints += $question->choices->sum('points');
            } else {
                $maxPoints = $question->choices->max('points') ?? 0;
                $totalPoints += $maxPoints;
            }
        }

        return $totalPoints;
    }

    private function isEmptyField($field): bool
    {
        return !isset($field) || empty($field);
    }
}