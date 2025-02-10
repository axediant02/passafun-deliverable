<?php

namespace App\Http\Controllers;

use App\Models\QuizStatus;
use Illuminate\Http\Request;
use App\Enums\QuizStatuses;

class QuizStatusController extends Controller
{
    public function getDefaultStatus(){
        return QuizStatuses::Unpublished;
    }

    public function index()
    {
        $statuses = QuizStatus::all();
        return response()->json($statuses);
    }

    public function show($id)
    {
        $status = QuizStatus::findOrFail($id);
        return response()->json($status);
    }

    public function store(Request $request)
    {
        $status = QuizStatus::create($request->all());
        return response()->json($status, 201);
    }

  public function update(Request $request, $id)
{
    // Check if request has no input
    if ($request->all() === []) {
        return response()->json([
            'success' => false,
            'message' => 'No data received for update.',
        ], 400); // 400 Bad Request
    }

    // Example validation for expected fields
    $validatedData = $request->validate([
        'quiz_status_id' => 'required|integer',
    ]);

    $status = QuizStatus::findOrFail($id);

    // Update status fields as needed
    if (isset($validatedData['quiz_status_id'])) {
        $status->status = $validatedData['quiz_status_id'];
    }

    $status->save(); // Save all changes

    return response()->json([
        'success' => true,
        'message' => 'Quiz status updated successfully.',
        'data' => $status,
    ]);
}


    public function destroy($id)
    {
        QuizStatus::destroy($id);
        return response()->json(null, 204);
    }
}
