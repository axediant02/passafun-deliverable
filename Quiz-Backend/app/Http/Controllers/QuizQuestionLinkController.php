<?php

namespace App\Http\Controllers;

use App\Models\QuizQuestionLink;
use Illuminate\Http\Request;

class QuizQuestionLinkController extends Controller
{
    public function index()
    {
        $links = QuizQuestionLink::all();
        return response()->json($links);
    }

    public function show($id)
    {
        $link = QuizQuestionLink::findOrFail($id);
        return response()->json($link);
    }

    public function store(Request $request)
    {
        $link = QuizQuestionLink::create($request->all());
        return response()->json($link, 201);
    }

    public function update(Request $request, $id)
    {
        $link = QuizQuestionLink::findOrFail($id);
        $link->update($request->all());
        return response()->json($link);
    }

    public function destroy($id)
    {
        QuizQuestionLink::destroy($id);
        return response()->json(null, 204);
    }
}
