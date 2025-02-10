<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipantQuizSummary\ParticipantDataSubmissionRequest;
use App\Models\Participant;
use App\Models\ParticipantAnswer;
use App\Models\ParticipantQuizSummary;
use App\Models\Result;
use Illuminate\Http\Request;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ParticipantQuizSummaryController extends Controller
{
    public function index()
    {
        $page = request('page', 1);
        $perPage = 5;

        $summaries = ParticipantQuizSummary::with(['participant', 'quiz'])
            ->orderByDesc('completed_at')
            ->paginate($perPage);

        $transformedSummaries = $summaries->map(function ($summary) {
            return [
                'id' => $summary->id,
                'participant' => $summary->participant->full_name,
                'quiz' => $summary->quiz->name,
                'score' => $summary->score,
                'completed_at' => $summary->completed_at,
            ];
        });

        return response()->json([
            'data' => $transformedSummaries,
            'total' => $summaries->total(),
            'per_page' => $perPage,
            'current_page' => (int)$page,
            'last_page' => $summaries->lastPage(),
            'next_page_url' => $summaries->nextPageUrl(),
            'prev_page_url' => $summaries->previousPageUrl(),
        ]);
    }

    public function indexPaginated()
    {
        $page = request('page', 1);
        $perPage = 10;

        $summaries = ParticipantQuizSummary::with(['participant', 'quiz'])
            ->orderByDesc('completed_at')
            ->get()
            ->groupBy(function ($summary) {
                return $summary->quiz_id . '_' . Carbon::parse($summary->completed_at)->format('Y-m-d H:i:05');
            })
            ->map(function ($group) {
                $firstSummary = $group->first();
                $participants = $group->pluck('participant.full_name')->toArray();

                return [
                    'id' => $firstSummary->id,
                    'quiz_id' => $firstSummary->quiz_id,
                    'quiz' => $firstSummary->quiz->name,
                    'quiz_image' => $this->imageService->getTemporaryImageUrl($firstSummary->quiz->thumbnail),
                    'participants' => $participants,
                    'participant_count' => count($participants),
                    'completed_at' => $firstSummary->completed_at,
                ];
            })
            ->sortByDesc(function ($item) {
                return Carbon::parse($item['completed_at'])->timestamp;
            })
            ->values();

        $total = $summaries->count();
        $items = $summaries->forPage($page, $perPage);

        return response()->json([
            'data' => $items->values()->all(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => (int)$page,
            'last_page' => ceil($total / $perPage),
            'next_page_url' => $page < ceil($total / $perPage) ? url()->current() . '?page=' . ($page + 1) : null,
            'prev_page_url' => $page > 1 ? url()->current() . '?page=' . ($page - 1) : null,
        ]);
    }

    public function showByParticipant($participantId)
    {

        $participant = Participant::with([
            'participantQuizSummaries.quiz.questions.choices',
            'participantQuizSummaries.quiz.results'
        ])
            ->findOrFail($participantId);

        $summaries = $participant->participantQuizSummaries()
            ->with(['quiz.questions.choices', 'quiz.results'])
            ->orderByDesc('completed_at')
            ->paginate(5);

        $summaries->getCollection()->transform(function ($summary) {
            $totalPoints = $summary->quiz->questions->reduce(function ($carry, $question) use ($summary) {
                $answer = $question->participantAnswers->firstWhere('participant_id', $summary->participant_id);
                if ($answer && $answer->choice) {
                    $selectedChoice = $question->choices->firstWhere('id', $answer->choice->id);
                    return $carry + ($selectedChoice->points ?? 0);
                }
                return $carry;
            }, 0);

            $score = $summary->score ?? $totalPoints;

            $result = $summary->quiz->results->firstWhere(function ($result) use ($score, $summary) {
                return $result->quiz_id === $summary->quiz_id &&
                    $result->min_points <= $score &&
                    $score <= $result->max_points;
            });

            $header = $result ? $result->header : 'TBD';

            return [
                'id' => $summary->id,
                'participant_id' => $summary->participant_id,
                'quiz_id' => $summary->quiz_id,
                'quiz_name' => $summary->quiz->name ?? 'Quiz not found',
                'score' => $score,
                'header' => $header,
                'completed_at' => $summary->completed_at,
            ];
        });

        $data = [
            'participant' => [
                'full_name' => $participant->full_name,
                'email' => $participant->email,
                'contact_number' => $participant->contact_number,
            ],
            'summaries' => $summaries,
        ];

        return response()->json($data);
    }

    public function showQuizSummary($summaryId)
{
    $summary = ParticipantQuizSummary::with([
        'participant',
        'quiz.questions.choices',
        'quiz.questions.participantAnswers',
        'result'
    ])->findOrFail($summaryId);

    $totalScore = $summary->quiz->questions->reduce(function ($carry, $question) use ($summary) {
        $answers = $question->participantAnswers->where('participant_id', $summary->participant->id);
        
        if ($question->questionType->id === 1) { 
            $maxPoints = $question->choices->max('points') ?? 0;
            return $carry + ($answers->count() > 0 ? $maxPoints : 0);
        }

        return $carry + $answers->sum(function($answer) use ($question) {
            if ($answer && $answer->choice) {
                $selectedChoice = $question->choices->firstWhere('id', $answer->choice->id);
                return $selectedChoice->points ?? 0;
            }
            return 0;
        });
    }, 0);

    return response()->json([
        'participant' => [
            'id' => $summary->participant->id,
            'full_name' => $summary->participant->full_name,
            'email' => $summary->participant->email,
            'contact_number' => $summary->participant->contact_number,
            'age' => $summary->participant->age,
        ],
        'quiz' => [
            'name' => $summary->quiz->name,
            'score' => $summary->score ?? $totalScore,
            'questions' => $summary->quiz->questions->map(function ($question) use ($summary) {
                $answers = $question->participantAnswers->where('summary_id', $summary->id);
                $questionImageUrl = $question->question_image ? $this->getTemporaryImageUrl($question->question_image) : null;

                return [
                    'id' => $question->id,
                    'text' => $question->question_text,
                    'image' => $questionImageUrl,
                    'choices' => $question->choices->map(function ($choice) {
                        $choiceImageUrl = $choice->choice_image ? $this->getTemporaryImageUrl($choice->choice_image) : null;
                        return [
                            'id' => $choice->id,
                            'choice_text' => $choice->choice_text,
                            'is_correct' => $choice->is_correct,
                            'points' => $choice->points,
                            'image' => $choiceImageUrl,
                        ];
                    }),
                    'answers' => collect($answers)->map(function ($answer) {
                        return $answer->choice ? $answer->choice->choice_text : $answer->answer_text;
                    })->values()->toArray(),
                    'question_type' => $question->questionType,
                ];
            }),
        ],
        'result' => $summary->result ? [
            'header' => $summary->result->header,
        ] : null,
    ]);
}

    public function showAnswersByQuiz($quizId)
    {
        $answers = ParticipantAnswer::where('quiz_id', $quizId)->with('participant')->get();

        return response()->json($answers);
    }

    public function showResultByUniqueResultId($uniqueResultId)
    {
        $participantQuizSummary = ParticipantQuizSummary::where('unique_result_id', $uniqueResultId)
            ->with('result')
            ->firstOrFail();

        if (!$participantQuizSummary->result) {
            return response()->json(['error' => 'Result not found for the given unique_result_id'], 404);
        }

        $result = $participantQuizSummary->result;
        $imageUrl = $this->imageService->getTemporaryImageUrl($result->image);

        $resultData = [
            'header' => $result->header,
            'description' => $result->description,
            'financial_tips' => $result->financial_tips,
            'image' => $imageUrl,
        ];

        return response()->json($resultData);
    }


    public function update(Request $request, $id)
    {
        $summary = ParticipantQuizSummary::findOrFail($id);
        $summary->update($request->all());

        return response()->json($summary);
    }


    public function destroy($id)
    {
        ParticipantQuizSummary::destroy($id);

        return response()->json(null, 204);
    }

    public function storeParticipantScore(array $validatedData)
    {
        $resultId = Result::where('quiz_id', $validatedData['quiz_id'])
            ->where('min_points', '<=', $validatedData['participant_score'])
            ->where('max_points', '>=', $validatedData['participant_score'])
            ->value('id');

        $summary = ParticipantQuizSummary::create([
            'participant_id' => $validatedData['participant_id'],
            'quiz_id' => $validatedData['quiz_id'],
            'score' => $validatedData['participant_score'],
            'result_id' => $resultId,
            'unique_result_id' => $validatedData['unique_result_id'],
            'completed_at' => now(),
        ]);

        return $summary;
    }

    public function storeParticipantSummary(ParticipantDataSubmissionRequest $request)
    {
        $validatedData = $request->validated();

        $participant = Participant::where('full_name', $validatedData['full_name'])
            ->where('email', $validatedData['email'])
            ->first();

        if ($participant) {
            if (empty($participant->contact_number)) {
                $participant->contact_number = $validatedData['contact_number'];
                $participant->save();
            }
            if ($participant->age !== $validatedData['age']) {
                $participant->age = $validatedData['age'];
                $participant->save();
            }
            if (empty($participant->age)) {
                $participant->age = $validatedData['age'];
                $participant->save();
            }
        } else {
            $participantWithSameName = Participant::where('full_name', $validatedData['full_name'])->first();

            if ($participantWithSameName) {
                $participant = Participant::create($validatedData);
            } else {
                $participant = Participant::create($validatedData);
            }
        }
        $validatedData['participant_id'] = $participant->id;

        $participantQuizSummaryId = $this->storeParticipantScore($validatedData);

        $validatedData['summary_id'] = $participantQuizSummaryId->id;

        (new ParticipantAnswerController)->storeAnswers($validatedData);

        return response()->json([
            'message' => 'Participant and quiz summary successfully created or updated',
        ], 201);
    }

    public function storeParticipantSummaryWithoutEmailCheck(ParticipantDataSubmissionRequest $request)
    {
        $validatedData = $request->validated();
        $participant = Participant::create($validatedData);
        $validatedData['participant_id'] = $participant->id;
        (new ParticipantAnswerController)->storeAnswers($validatedData);
        $this->storeParticipantScore($validatedData);
        return response()->json([
            'message' => 'Participant and quiz summary successfully created',
        ], 201);
    }

    private function getTemporaryImageUrl($path)
    {
        return $this->imageService->getTemporaryImageUrl($path);
    }

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
}
