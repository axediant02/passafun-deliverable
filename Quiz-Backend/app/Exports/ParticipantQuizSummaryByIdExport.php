<?php

namespace App\Exports;

use App\Models\ParticipantQuizSummary;
use App\Http\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ParticipantQuizSummaryByIdExport extends BaseParticipantQuizSummaryExport implements FromQuery, WithHeadings
{
    protected $participantId;
    protected $selectedFields;
    protected $quizId;
    protected $summaryId;
    protected $dateRange;
    protected $selectedQuizzes;

    public function __construct(
        array $selectedFields,
        $participantId,
        $quizId = null,
        $summaryId = null,
        array $dateRange = [],
        array $selectedQuizzes = []
    ) {
        $dateRange = is_array($dateRange) ? $dateRange : [];

        parent::__construct(
            $selectedFields,
            $dateRange,
            $participantId,
            $quizId,
            $summaryId,
            $selectedQuizzes
        );

        $this->summaryId = $summaryId;
        $this->dateRange = $dateRange;
        $this->selectedQuizzes = $selectedQuizzes;
    }

    public function query()
    {
        $query = parent::query();

        if ($this->participantId) {
            $query->where('participant_quiz_summaries.participant_id', $this->participantId);
        }

        if ($this->summaryId) {
            $query->where('participant_quiz_summaries.id', $this->summaryId);
        }

        if ($this->quizId) {
            $query->where('participant_quiz_summaries.quiz_id', $this->quizId);
        }

        return $query;
    }

    protected function getSelectedFields()
    {
        $fieldMapping = [
            'full_name' => 'participants.full_name',
            'email' => 'participants.email',
            'contact_number' => 'participants.contact_number',
            'age' => 'participants.age',
            'name' => 'quizzes.name',
            'score' => 'participant_quiz_summaries.score',
            'completed_at' => 'participant_quiz_summaries.completed_at',
            'header' => 'results.header',
        ];

        return array_filter(array_map(function ($field) use ($fieldMapping) {
            return $fieldMapping[$field] ?? null;
        }, $this->selectedFields));
    }

    public function headings(): array
    {
        $fieldLabels = [
            'name' => 'Quiz Name',
            'full_name' => 'Full Name',
            'contact_number' => 'Phone Number',
            'email' => 'Email Address',
            'age' => 'Age',
            'score' => 'Score',
            'completed_at' => 'Submission Date',
            'header' => 'Result'
        ];

        $headings = [];

        foreach ($this->selectedFields as $field) {
            if (isset($fieldLabels[$field])) {
                $headings[] = $fieldLabels[$field];
            }
        }

        if (in_array('questions', $this->selectedFields) || in_array('answers', $this->selectedFields)) {
            $query = ParticipantQuizSummary::with(['quiz.questions' => function ($query) {
                $query->orderBy('id');
            }])
            ->where('participant_id', $this->participantId);

            if ($this->summaryId) {
                $query->where('id', $this->summaryId);
            }

            if ($this->quizId) {
                $query->where('quiz_id', $this->quizId);
            }

            $summary = $query->first();

            if ($summary && $summary->quiz && $summary->quiz->questions) {
                foreach ($summary->quiz->questions as $index => $question) {
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
