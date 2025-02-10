<?php

namespace App\Http\Controllers;

use App\Models\QuestionType;
use Illuminate\Http\Request;

class QuestionTypeController extends Controller
{
    public function index()
    {
        $questionTypes = QuestionType::all();
        return response()->json($questionTypes);
    }

    public function show($id)
    {
        $questionType = QuestionType::findOrFail($id);
        return response()->json($questionType);
    }

    public function store(Request $request)
    {
        $questionType = QuestionType::create($request->all());
        return response()->json($questionType, 201);
    }

    public function update(Request $request, $id)
    {
        $questionType = QuestionType::findOrFail($id);
        $questionType->update($request->all());
        return response()->json($questionType);
    }

    public function destroy($id)
    {
        QuestionType::destroy($id);
        return response()->json(null, 204);
    }
}