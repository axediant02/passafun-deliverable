<?php

namespace App\Http\Controllers;

use App\Models\ParticipantAnswer;
use Illuminate\Http\Request;

class ParticipantAnswerController extends Controller
{
    public function index()
    {
        $participantAnswers = ParticipantAnswer::all();
        return response()->json($participantAnswers);
    }

    public function show($id)
    {
        $participantAnswer = ParticipantAnswer::findOrFail($id);
        return response()->json($participantAnswer);
    }

    public function store(Request $request)
    {
        $participantAnswer = ParticipantAnswer::create($request->all());
        return response()->json($participantAnswer, 201);
    }


    public function storeAnswers($validatedData)
    {
        $participantAnswersData = collect($validatedData['answers'])->flatMap(function ($participantAnswer) use ($validatedData) {
            if (!empty($participantAnswer['choice_id'])) {
   
                $choiceIds = is_array($participantAnswer['choice_id']) 
                    ? $participantAnswer['choice_id']
                    : [$participantAnswer['choice_id']];

                return collect($choiceIds)
                    ->filter() 
                    ->map(function ($choiceId) use ($validatedData, $participantAnswer) {
                        return [
                            'participant_id' => $validatedData['participant_id'],
                            'quiz_id' => $validatedData['quiz_id'],
                            'question_id' => $participantAnswer['question_id'],
                            'choice_id' => $choiceId,
                            'answer_text' => null,
                            'summary_id' => $validatedData['summary_id'],
                        ];
                    });
            } else {

                return [
                    [
                        'participant_id' => $validatedData['participant_id'],
                        'quiz_id' => $validatedData['quiz_id'],
                        'question_id' => $participantAnswer['question_id'],
                        'choice_id' => null,
                        'answer_text' => $participantAnswer['open_ended_response'],
                        'summary_id' => $validatedData['summary_id'],
                    ],
                ];
            }
        })->toArray();

        ParticipantAnswer::insert($participantAnswersData);

        return [
            'message' => 'Participant answers successfully inserted',
        ];
    }

    public function update(Request $request, $id)
    {
        $participantAnswer = ParticipantAnswer::findOrFail($id);
        $participantAnswer->update($request->all());
        return response()->json($participantAnswer);
    }

    public function destroy($id)
    {
        ParticipantAnswer::destroy($id);
        return response()->json(null, 204);
    }
}
