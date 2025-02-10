<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\ParticipantQuizSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ParticipantController extends Controller
{
    public function index()
    {
        $participants = ParticipantQuizSummary::with(['participant', 'quiz', 'result'])
            ->select('participant_quiz_summaries.*')
            ->join('participants', 'participants.id', '=', 'participant_quiz_summaries.participant_id')
            ->orderBy('participant_quiz_summaries.completed_at', 'desc')
            ->paginate(5);

        return response()->json($participants);
    }

    public function search(Request $request)
    {
        $query = strtolower($request->input('query'));

        $participants = Participant::with('participantQuizSummaries.quiz', 'participantQuizSummaries.result')
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($subQuery) use ($query) {
                    $subQuery->whereRaw('LOWER(participants.full_name) ILIKE ?', ['%' . $query . '%'])
                        ->orWhereRaw('LOWER(participants.email) ILIKE ?', ['%' . $query . '%'])
                        ->orWhereRaw('LOWER(participants.contact_number) ILIKE ?', ['%' . $query . '%'])
                        ->orWhereHas('participantQuizSummaries.quiz', function ($quizQuery) use ($query) {
                            $quizQuery->whereRaw('LOWER(name) ILIKE ?', ['%' . $query . '%']);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return response()->json($participants);
    }

    public function showParticipantsByQuizId($quizId)
    {
        $participants = Participant::whereHas('participantQuizzes', function ($query) use ($quizId) {
            $query->where('quiz_id', $quizId);
        })
            ->with(['participantQuizSummaries' => function ($query) {
                $query->select('id', 'participant_id', 'score', 'result_id');
            }, 'participantQuizzes' => function ($query) use ($quizId) {
                $query->where('quiz_id', $quizId);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $resultHeaders = DB::table('results')
            ->where('quiz_id', $quizId)
            ->pluck('header');

        foreach ($participants as $participant) {
            foreach ($participant->participantQuizSummaries as $summary) {
                $summary->result_headers = $resultHeaders->toArray();
            }
        }

        return response()->json([
            'participants' => $participants,
        ]);
    }

    public function show($id)
    {
        $participant = Participant::findOrFail($id);
        return response()->json($participant);
    }

    public function store(Request $request)
    {
        $participant = Participant::create($request->all());
        return response()->json($participant, 201);
    }


    public function storeParticipant($validatedData)
    {
        $participant = Participant::create([
            'full_name' => $validatedData['full_name'],
            'email' => $validatedData['email'],
            'contact_number' => $validatedData['contact_number'],
            'age' => $validatedData['age'],
        ]);
        return [
            'participant_id' => $participant->id,
            'message' => 'Participant successfully created',
        ];
    }

    public function update(Request $request, $id)
    {
        $participant = Participant::findOrFail($id);
        $participant->update($request->all());
        return response()->json($participant);
    }

    public function destroy($id)
    {
        Participant::destroy($id);
        return response()->json(null, 204);
    }


    public function fetchParticipantByUniqueId($uniqueResultId)
    {
        $participantFullName = DB::table('participant_quiz_summaries')
            ->join('participants', 'participant_quiz_summaries.participant_id', '=', 'participants.id')
            ->where('participant_quiz_summaries.unique_result_id', $uniqueResultId)
            ->value('participants.full_name');

        return response()->json(['full_name' => $participantFullName ?? 'Participant not found']);
    }

    public function filterParticipantData(Request $request)
    {
        $selectedQuizzes = $request->input('selectedQuizzes', []);
        $fromDate = $request->input('fromDate', null);
        $toDate = $request->input('toDate', null);
        $selectedParticipantInformations = $request->input('selectedParticipantInformations', []);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 5);

        try {
            $fromDate = $fromDate ? Carbon::parse($fromDate)->startOf('day') : null;
            $toDate = $toDate ? Carbon::parse($toDate)->endOf('day') : null;

            $participantsQuery = ParticipantQuizSummary::query()
                ->join('participants', 'participant_quiz_summaries.participant_id', '=', 'participants.id')
                ->join('quizzes', 'participant_quiz_summaries.quiz_id', '=', 'quizzes.id')
                ->leftJoin('results', 'participant_quiz_summaries.result_id', '=', 'results.id')
                ->select(
                    'participants.id',
                    'participants.full_name',
                    'participants.email',
                    'participants.contact_number',
                    'participants.age',
                    'participant_quiz_summaries.id as summary_id',
                    'participant_quiz_summaries.score',
                    'participant_quiz_summaries.completed_at',
                    'quizzes.name as quiz_name',
                    'results.header as result'
                );

            if ($fromDate && $toDate && $fromDate->isSameDay($toDate)) {
                $participantsQuery->whereDate('participant_quiz_summaries.completed_at', $fromDate->toDateString());
            } else {
                if ($fromDate) {
                    $participantsQuery->where('participant_quiz_summaries.completed_at', '>=', $fromDate);
                }
                if ($toDate) {
                    $participantsQuery->where('participant_quiz_summaries.completed_at', '<=', $toDate);
                }
            }

            if (!empty($selectedQuizzes)) {
                $participantsQuery->whereIn('quizzes.name', $selectedQuizzes);
            }

            if (!empty($selectedParticipantInformations)) {
                foreach ($selectedParticipantInformations as $info) {
                    $participantsQuery->whereNotNull("participants.$info");
                }
            }

            $participants = $participantsQuery
                ->orderBy('participant_quiz_summaries.completed_at', 'desc')
                ->paginate($perPage);

            return response()->json($participants);

        } catch (\Exception $e) {
            \Log::error('Filter error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Error filtering participants'], 500);
        }
    }

}
