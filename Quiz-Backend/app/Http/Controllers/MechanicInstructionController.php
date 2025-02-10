<?php

namespace App\Http\Controllers;

use App\Models\MechanicInstruction;
use Illuminate\Http\Request;

class MechanicInstructionController extends Controller
{
    public function index()
    {
        $instructions = MechanicInstruction::all();
        return response()->json($instructions);
    }

    public function show($id)
    {
        $instruction = MechanicInstruction::findOrFail($id);
        return response()->json($instruction);
    }

    public function store(Request $request)
    {
        $instruction = MechanicInstruction::create($request->all());
        return response()->json($instruction, 201);
    }

    public function update(Request $request, $id)
    {
        $instruction = MechanicInstruction::findOrFail($id);
        $instruction->update($request->all());
        return response()->json($instruction);
    }

    public function destroy($id)
    {
        MechanicInstruction::destroy($id);
        return response()->json(null, 204);
    }
}
