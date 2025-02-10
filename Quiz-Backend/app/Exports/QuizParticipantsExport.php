<?php

namespace App\Exports;

use App\Models\ParticipantQuizSummary;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
use App\Models\Quiz;

class QuizParticipantsExport extends BaseParticipantQuizSummaryExport implements FromQuery, WithHeadings
{
    protected $quizId;
    protected $selectedFields;
    protected $dateRange;

    public function __construct(array $selectedFields, $quizId, array $dateRange = [])
    {
        parent::__construct($selectedFields, $dateRange, null, $quizId);
        $this->quizId = $quizId;
        $this->dateRange = $dateRange;
    }

    public function query()
{
    $query = ParticipantQuizSummary::query()
        ->with([
            'participant',
            'quiz.questions.choices',
            'participantAnswers',
            'result'
        ])
        ->select([
            'participant_quiz_summaries.*',
            'participants.*'
        ])
        ->join('participants', 'participants.id', '=', 'participant_quiz_summaries.participant_id')
        ->where('quiz_id', $this->quizId);

    if (!empty($this->dateRange)) {
        if (isset($this->dateRange['from']) || isset($this->dateRange['to'])) {
            $fromDate = isset($this->dateRange['from']) ? Carbon::parse($this->dateRange['from'])->startOfDay() : null;
            $toDate = isset($this->dateRange['to']) ? Carbon::parse($this->dateRange['to'])->endOfDay() : null;

            if ($fromDate && $toDate && $fromDate->isSameDay($toDate)) {
                $query->whereDate('participant_quiz_summaries.completed_at', $fromDate->toDateString());
            } else {
                if ($fromDate) {
                    $query->where('participant_quiz_summaries.completed_at', '>=', $fromDate);
                }
                if ($toDate) {
                    $query->where('participant_quiz_summaries.completed_at', '<=', $toDate);
                }
            }
        }
    }

    return $query->orderBy('participant_quiz_summaries.completed_at', 'desc');
}


    public function headings(): array
    {
        $fieldLabels = [
            'full_name' => 'Full Name',
            'contact_number' => 'Phone Number',
            'email' => 'Email Address',
            'age' => 'Age',
            'score' => 'Score',
            'completed_at' => 'Submission Date',
            'header' => 'Result',
        ];

        $headings = [];
        foreach ($this->selectedFields as $field) {
            if (isset($fieldLabels[$field])) {
                $headings[] = $fieldLabels[$field];
            }
        }

        if (in_array('questions', $this->selectedFields) || in_array('answers', $this->selectedFields)) {
            $quiz = Quiz::find($this->quizId); 
            if ($quiz && $quiz->questions) {
                foreach ($quiz->questions as $index => $question) {
                    if (in_array('questions', $this->selectedFields)) {
                        $headings[] = "Question " . ($index + 1);
                    }
                    if (in_array('answers', $this->selectedFields)) {
                        $headings[] = "Answer " . ($index + 1);
                    }
                }
            }
        }

        return $headings;
    }
}
