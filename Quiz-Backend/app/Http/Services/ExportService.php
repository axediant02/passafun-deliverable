<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\ParticipantQuizSummary;

class ExportService
{
    private $columnWidths = [
        'name' => 20,
        'full_name' => 30,
        'contact_number' => 15,
        'email' => 35,
        'age' => 8,
        'created_at' => 15,
        'quiz_name' => 25,
        'score' => 10,
        'result' => 15,
        'completed_at' => 15,
        'header' => 20,
        'questions_and_answers' => 50,
    ];

    public function getColumnWidths(array $selectedFields):array
    {
        $widths = [];
        foreach($selectedFields as $index => $field) {
            if (isset($this->columnWidths[$field])) {
                $widths[chr(65 + $index)] = $this->columnWidths[$field];
            }
        }
        return $widths;
    }

    public function buildQuery(array $dateRange, ?int $participantId = null, ?int $quizId = null, ?int $summaryId = null)
    {
        $query = ParticipantQuizSummary::query()
            ->with([
                'participant',
                'quiz.questions',
                'quiz.questions.choices',
                'participantAnswers',
                'result',
                'quiz'
            ])
            ->select($this->getSelectColumns());

        $query->join('participants', 'participants.id', '=', 'participant_quiz_summaries.participant_id')
            ->leftJoin('quizzes', 'quizzes.id', '=', 'participant_quiz_summaries.quiz_id')
            ->leftJoin('results', 'results.id', '=', 'participant_quiz_summaries.result_id');

        if ($summaryId) {
            $query->where('participant_quiz_summaries.id', (int) $summaryId);
        }

        if ($quizId) {
            $query->where('participant_quiz_summaries.quiz_id', (int) $quizId); // Fixed from $this->quizId
        }

        if ($participantId) {
            $query->where('participant_quiz_summaries.participant_id', (int) $participantId);
        }

        if ($participantId || $summaryId || $quizId) {
           
            $this->applyCompletedDateRange($query, $dateRange);
        } else {
           
            $this->applyCreatedDateRange($query, $dateRange);
        }

        return $query->orderBy('participants.created_at', 'desc')
                    ->orderBy('participant_quiz_summaries.completed_at', 'desc');
    }

    private function getSelectColumns():array
    {
        return [
            'participant_quiz_summaries.id as quiz_summary_id',
            'participant_quiz_summaries.participant_id',
            'participant_quiz_summaries.quiz_id',
            'participant_quiz_summaries.score',
            'participant_quiz_summaries.result_id',
            'participant_quiz_summaries.completed_at',
            'participants.id as participant_id',
            'participants.contact_number',
            'participants.email',
            'participants.full_name',
            'participants.age',
            'participants.created_at',
        ];
    }

    private function applyCreatedDateRange($query, array $dateRange)
    {
        if (empty($dateRange)) {
            return $query;
        }

        $timezone = config('app.timezone');

        if (!empty($dateRange['from']) && !empty($dateRange['to']) && 
            $dateRange['from'] === $dateRange['to']) {
            $date = Carbon::parse($dateRange['from'])
                ->setTimezone($timezone);
            
            $query->whereDate('participants.created_at', $date->format('Y-m-d'));
            return $query;
        }

        if (!empty($dateRange['from'])) {
            $from = Carbon::parse($dateRange['from'])
                ->setTimezone($timezone)
                ->startOfDay();
            $query->whereDate('participants.created_at', '>=', $from);
        }

        if (!empty($dateRange['to'])) {
            $to = Carbon::parse($dateRange['to'])
                ->setTimezone($timezone)
                ->endOfDay();
            $query->whereDate('participants.created_at', '<=', $to);
        }

        return $query;
    }

    private function applyCompletedDateRange($query, array $dateRange)
    {
        if (empty($dateRange)) {
            return $query;
        }

        $timezone = config('app.timezone');

        if (!empty($dateRange['from']) && !empty($dateRange['to']) && 
            $dateRange['from'] === $dateRange['to']) {
            $date = Carbon::parse($dateRange['from'])
                ->setTimezone($timezone);
            
            $query->whereDate('participant_quiz_summaries.completed_at', $date->format('Y-m-d'));
            return $query;
        }

        if (!empty($dateRange['from'])) {
            $from = Carbon::parse($dateRange['from'])
                ->setTimezone($timezone)
                ->startOfDay();
            $query->whereDate('participant_quiz_summaries.completed_at', '>=', $from);
        }

        if (!empty($dateRange['to'])) {
            $to = Carbon::parse($dateRange['to'])
                ->setTimezone($timezone)
                ->endOfDay();
            $query->whereDate('participant_quiz_summaries.completed_at', '<=', $to);
        }

        return $query;
    }

    public function buildQuizParticipantsQuery($dateRange, $quizId)
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
            ->where('quiz_id', $quizId);

        if (!empty($dateRange)) {
            if (isset($dateRange['from']) && isset($dateRange['to'])) {
                $fromDate = Carbon::parse($dateRange['from'])->startOfDay();
                $toDate = Carbon::parse($dateRange['to'])->endOfDay();
                $query->whereBetween('participant_quiz_summaries.completed_at', [$fromDate, $toDate]);
            }
        }

        return $query->orderBy('participant_quiz_summaries.completed_at', 'desc');
    }
}
