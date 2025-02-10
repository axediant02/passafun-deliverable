<?php

namespace App\Http\Controllers;

use App\Models\ParticipantQuiz;
use Illuminate\Http\Request;

class ParticipantQuizController extends Controller
{
    public function index()
    {
        $participantQuizzes = ParticipantQuiz::all();
        return response()->json($participantQuizzes);
    }

    public function show($id)
    {
        $participantQuiz = ParticipantQuiz::findOrFail($id);
        return response()->json($participantQuiz);
    }

    public function store(Request $request)
    {
        $participantQuiz = ParticipantQuiz::create($request->all());
        return response()->json($participantQuiz, 201);
    }

    public function update(Request $request, $id)
    {
        $participantQuiz = ParticipantQuiz::findOrFail($id);
        $participantQuiz->update($request->all());
        return response()->json($participantQuiz);
    }

    public function destroy($id)
    {
        ParticipantQuiz::destroy($id);
        return response()->json(null, 204);
    }
}
