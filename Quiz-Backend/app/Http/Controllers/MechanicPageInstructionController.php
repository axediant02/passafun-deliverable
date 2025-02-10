<?php

namespace App\Http\Controllers;

use App\Models\MechanicPageInstruction;
use Illuminate\Http\Request;

class MechanicPageInstructionController extends Controller
{
    public function index()
    {
        $instructions = MechanicPageInstruction::with('mechanicPage', 'mechanicInstruction')->get();
        return response()->json($instructions);
    }

    public function show($id)
    {
        $instruction = MechanicPageInstruction::with('mechanicPage', 'mechanicInstruction')->findOrFail($id);
        return response()->json($instruction);
    }

    public function store(Request $request)
    {
        $instruction = MechanicPageInstruction::create($request->all());
        return response()->json($instruction, 201);
    }

    public function update(Request $request, $id)
    {
        $instruction = MechanicPageInstruction::findOrFail($id);
        $instruction->update($request->all());
        return response()->json($instruction);
    }

    public function destroy($id)
    {
        MechanicPageInstruction::destroy($id);
        return response()->json(null, 204);
    }
}
