<?php

namespace App\Exports;

use App\Enums\QuestionTypeEnums;
use App\Http\Services\ExportService;
use App\Models\ParticipantQuizSummary;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Carbon\Carbon;

class BaseParticipantQuizSummaryExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping, WithColumnWidths
{
    protected $dateRange = [];
    protected $selectedFields;
    protected $exportService;
    protected $participantId;
    protected $quizId;
    protected $summaryId;
    protected $selectedQuizzes = [];
    protected $fieldOrder = [
        'name',
        'full_name',
        'contact_number',
        'email',
        'age',
        'created_at',
        'quiz_name',
        'score',
        'result',
        'completed_at',
        'header',
        'questions_and_answers',
        'questions',
        'answers',
    ];

    public function __construct(
        array $selectedFields,
        array $dateRange = [],
        ?int $participantId = null,
        ?int $quizId = null,
        ?int $summaryId = null,
        array $selectedQuizzes = []
    ) {
        $this->selectedFields = array_intersect($this->fieldOrder, $selectedFields);
        $this->dateRange = $dateRange;
        $this->participantId = $participantId;
        $this->quizId = $quizId;
        $this->summaryId = $summaryId;
        $this->selectedQuizzes = $selectedQuizzes;
        $this->exportService = new ExportService();
    }

    public function columnWidths():array
    {
        return $this->exportService->getColumnWidths($this->selectedFields);
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
                'participants.*',
                'quizzes.name as quiz_name'
            ])
            ->join('participants', 'participants.id', '=', 'participant_quiz_summaries.participant_id')
            ->join('quizzes', 'quizzes.id', '=', 'participant_quiz_summaries.quiz_id')
            ->leftJoin('results', 'participant_quiz_summaries.result_id', '=', 'results.id');

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

        if (!empty($this->selectedQuizzes)) {
            $query->whereIn('quizzes.id', $this->selectedQuizzes);
        }

        return $query->orderBy('participant_quiz_summaries.completed_at', 'desc');
    }

    public function map($participantQuizSummary): array
    {
        $exportData = [];

        foreach ($this->selectedFields as $fieldName) {
            if (!in_array($fieldName, ['questions', 'answers'])) {
                $exportData[] = $this->getFieldData($participantQuizSummary, $fieldName);
            }
        }

        if (in_array('questions', $this->selectedFields) || in_array('answers', $this->selectedFields)) {
            $questions = $this->getQuestions($participantQuizSummary);
            $answers = $this->getAnswers($participantQuizSummary);

            $totalQuestions = count($questions);
            for ($questionIndex = 0; $questionIndex < $totalQuestions; $questionIndex++) {
                if (in_array('questions', $this->selectedFields)) {
                    $exportData[] = $questions[$questionIndex] ?? 'No Question';
                }
                if (in_array('answers', $this->selectedFields)) {
                    $exportData[] = isset($answers[$questionIndex]) ? $answers[$questionIndex] : 'No Answer';
                }
            }
        }

        return $exportData;
    }

    private function getFieldData($participantQuizSummary, $field)
    {
        $fieldMapping = [
            'name' => fn() => $this->getQuizName($participantQuizSummary),
            'full_name' => fn() => $this->getParticipantFullName($participantQuizSummary),
            'contact_number' => fn() => $this->getContactNumber($participantQuizSummary),
            'email' => fn() => $this->getEmail($participantQuizSummary),
            'age' => fn() => $this->getAge($participantQuizSummary),
            'created_at' => fn() => $this->getCreatedAt($participantQuizSummary),
            'quiz_name' => fn() => $this->getQuizName($participantQuizSummary),
            'score' => fn() => $this->getScore($participantQuizSummary),
            'result' => fn() => $this->getResultHeader($participantQuizSummary),
            'completed_at' => fn() => $this->getCompletedAt($participantQuizSummary),
            'header' => fn() => $this->getResultHeader($participantQuizSummary),
            'questions_and_answers' => fn() => $this->getAlternatingQuestionsAndAnswers($participantQuizSummary),
            'questions' => fn() => $this->getQuestions($participantQuizSummary),
            'answers' => fn() => $this->getAnswers($participantQuizSummary),
        ];

        return $fieldMapping[$field]();
    }

    private function getQuizName($participantQuizSummary)
    {
        return optional($participantQuizSummary->quiz)->name;
    }

    private function getParticipantFullName($participantQuizSummary)
    {
        return optional($participantQuizSummary->participant)->full_name;
    }

    private function getContactNumber($participantQuizSummary)
    {
        return optional($participantQuizSummary->participant)->contact_number;
    }

    private function getEmail($participantQuizSummary)
    {
        return optional($participantQuizSummary->participant)->email;
    }

    private function getCreatedAt($participantQuizSummary)
    {
        $createdAt = $participantQuizSummary->participant->created_at;
        return "\t" . Carbon::parse($createdAt)->format('Y-m-d H:i:s');
    }

    private function getAge($participantQuizSummary)
    {
        return optional($participantQuizSummary->participant)->age;
    }

    private function getScore($participantQuizSummary)
    {
        return $participantQuizSummary->score;
    }

    private function getCompletedAt($participantQuizSummary)
    {
        $completedAt = $participantQuizSummary->completed_at;
        return "\t" . Carbon::parse($completedAt)->format('Y-m-d H:i:s');
    }

    private function getResultHeader($participantQuizSummary)
    {
        return optional($participantQuizSummary->result)->header;
    }

    private function getQuestions($participantQuizSummary)
    {
        if (!$participantQuizSummary->quiz || !$participantQuizSummary->quiz->questions) {
            return [];
        }

        return $participantQuizSummary->quiz->questions
            ->pluck('question_text')
            ->toArray();
    }

    private function getAnswers($participantQuizSummary)
    {
        if (!$participantQuizSummary->quiz || !$participantQuizSummary->quiz->questions) {
            return [];
        }

        return $participantQuizSummary->quiz->questions->map(function ($question) use ($participantQuizSummary) {
            $answer = $question->participantAnswers
                ->where('participant_id', $participantQuizSummary->participant_id)
                ->first();
            return $this->getAnswerText($answer, $question);
        })->toArray();
    }

    private function getAlternatingQuestionsAndAnswers($participantQuizSummary): array
    {
        $questions = $this->getQuestions($participantQuizSummary);
        $answers = $this->getAnswers($participantQuizSummary);

        $alternatingData = [];
        $maxCount = min(count($questions), count($answers));

        for ($index = 0; $index < $maxCount; $index++) {
            $alternatingData[] = $questions[$index] ?? 'No Question';
            $alternatingData[] = $answers[$index] ?? 'No Answer';
        }

        return $alternatingData;
    }

    private function getAnswerText($answer, $question)
    {
        if (!$answer) {
            return 'No Answer';
        }

        if ($question->question_type_id === QuestionTypeEnums::OpenEnded) {
            return $answer->answer_text ?? 'No answer provided';
        }

        if ($question->question_type_id === QuestionTypeEnums::MultipleSelect) {
            return $this->getMultipleSelectAnswerText($answer, $question);
        }

        return $this->getSingleChoiceAnswerText($answer, $question);
    }

    private function getMultipleSelectAnswerText($answer, $question)
    {
        $selectedChoices = collect($question->participantAnswers)
            ->filter(fn($participantAnswer) => 
                $participantAnswer->participant_id === $answer->participant_id &&
                $participantAnswer->question_id === $question->id
            )
            ->map(function ($participantAnswer) use ($question) {
                try {
                    $choiceId = is_array($participantAnswer)
                        ? $participantAnswer['choice_id']
                        : $participantAnswer->choice_id;

                    $choice = collect($question->choices)->firstWhere('id', $choiceId);
                    return $choice ? $choice['choice_text'] : null;
                } catch (\Exception $e) {
                    \Log::error('Error processing answer:', [
                        'error' => $e->getMessage(),
                        'participantAnswer' => $participantAnswer,
                    ]);
                    return null;
                }
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

            return !empty($selectedChoices) ? implode(' && ', $selectedChoices) : 'No Answer';
    }

    private function getSingleChoiceAnswerText($answer, $question)
    {
        $choice = collect($question->choices)->firstWhere('id', $answer->choice_id);
        return $choice ? $choice['choice_text'] : $answer->answer_text ?? 'No answer';
    }

    public function headings(): array
    {

        $headings = $this->getSelectedFieldsHeadings();

        if (in_array('questions', $this->selectedFields) || in_array('answers', $this->selectedFields)) {

            $quizSummary = ParticipantQuizSummary::with('quiz.questions')->first();
            $questions = $quizSummary && $quizSummary->quiz ? $quizSummary->quiz->questions : [];

            if (in_array('questions', $this->selectedFields)) {
                foreach ($questions as $index => $question) {
                    $headings[] = 'Question ' . ($index + 1);
                }
            }

            if (in_array('answers', $this->selectedFields)) {
                foreach ($questions as $index => $question) {
                    $headings[] = 'Answer for Question ' . ($index + 1);
                }
            }
        }

        $orderedHeadings = [];
        foreach ($this->fieldOrder as $field) {
            if (in_array($field, $this->selectedFields)) {

                $orderedHeadings[] = $headings[array_search($field, $this->selectedFields)];
            }
        }

        if (in_array('questions', $this->selectedFields) && in_array('answers', $this->selectedFields)) {
            $orderedHeadings = array_merge($orderedHeadings, $this->getQuestionsAndAnswersHeadings($quizSummary));
        }

        return $orderedHeadings;
    }

    private function getSelectedFieldsHeadings()
    {
        $fieldLabels = [
            'name' => 'Quiz Name',
            'full_name' => 'Full Name',
            'contact_number' => 'Phone Number',
            'email' => 'Email Address',
            'age' => 'Age',
            'created_at' => 'Date Created',
            'quiz_name' => 'Quiz Name',
            'score' => 'Score',
            'result' => 'Result',
            'completed_at' => 'Date Created',
            'header' => 'Result',
            'questions_and_answers' => 'Questions and Answers',
            'questions' => 'Questions',
            'answers' => 'Answers',
        ];

        return array_map(function ($field) use ($fieldLabels) {
            return $fieldLabels[$field] ?? 'N/A';
         }, $this->selectedFields);
    }
    }
    $selectedFields = ['full_name', 'email', 'quiz_name', 'score', 'result', 'completed_at'];
    $export = new BaseParticipantQuizSummaryExport($selectedFields);
