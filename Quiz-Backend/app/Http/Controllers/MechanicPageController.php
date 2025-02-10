<?php

namespace App\Http\Controllers;

use App\Http\Requests\MechanicPage\UpdateMechanicPageRequest;
use App\Models\MechanicInstruction;
use App\Models\MechanicPage;
use Illuminate\Http\Request;

class MechanicPageController extends Controller
{
    public function index()
    {
        $mechanicPages = MechanicPage::all();
        return response()->json($mechanicPages);
    }

    public function show($id)
    {
        $mechanicPage = MechanicPage::with(['mechanicPageInstructions.mechanicInstruction'])-> findOrFail($id);
        return response()->json([
            'id' => $mechanicPage->id,
         'header' => $mechanicPage->header,
         'instructions' => $mechanicPage->mechanicPageInstructions
        ]);
    }

    public function store(Request $request)
    {
        $mechanicPage = MechanicPage::create($request->all());
        return response()->json($mechanicPage, 201);
    }

    public function update(UpdateMechanicPageRequest $request, $id)
    {
        $validatedData = $request->validated();
        $mechanicPage = MechanicPage::updateOrCreate(
            ['id' => $id],
        );

        $mechanicPage->mechanicPageInstructions()->delete();

        collect($validatedData['mechanicInstructions'])->each(function ($instruction) use ($mechanicPage) {
            $mechanicInstruction = MechanicInstruction::updateOrCreate(
                ['id' => $instruction['instruction_id'] ?? null],
                ['instruction' => $instruction['instruction']]
            );

            $mechanicPage->mechanicPageInstructions()->create([
                'instruction_id' => $mechanicInstruction->id,
                'mechanic_page_id' => $mechanicPage->id
            ]);
        });

        $mechanicPage->load('mechanicPageInstructions.mechanicInstruction');

        return response()->json([
            'id' => $mechanicPage->id,
            'header' => $mechanicPage->header,
            'instructions' => $mechanicPage->mechanicPageInstructions
        ]);
    }
    
    
    protected function findMechanicPageOrFail($id)
    {
        return MechanicPage::with('mechanicPageInstructions.mechanicInstruction')
            ->findOrFail($id);
    }
    
    protected function createOrUpdateMechanicInstruction($instructionData)
    {
        return MechanicInstruction::updateOrCreate(
            ['id' => $instructionData['instruction_id'] ?? null],
            ['instruction' => $instructionData['instruction']]
        );
    }
    
    protected function linkInstructionToPage(MechanicPage $mechanicPage, $instructionId)
    {
        return $mechanicPage->mechanicPageInstructions()->create([
            'instruction_id' => $instructionId,
            'mechanic_page_id' => $mechanicPage->id
        ]);
    }
    
    public function destroy($id, $instructionId)
    {
        $mechanicPage = $this->findMechanicPageWithInstructions($id);
        $mechanicPageInstruction = $this->findMechanicPageInstruction($mechanicPage, $instructionId);
        if (!$mechanicPageInstruction) return $this->errorResponse('Instruction not found', 404);
        try {
            $this->deleteMechanicPageInstruction($mechanicPageInstruction);
            $this->deleteMechanicInstruction($instructionId);
        } catch (\Exception $e) {
            return $this->errorResponse('Could not delete instruction', 500);
        }
        return response()->json(['message' => 'Instruction deleted successfully']);
    }
    
    private function findMechanicPageWithInstructions($id)
    {
        return MechanicPage::with('mechanicPageInstructions')->findOrFail($id);
    }
    
    private function findMechanicPageInstruction(MechanicPage $mechanicPage, $instructionId)
    {
        return $mechanicPage->mechanicPageInstructions()->where('instruction_id', $instructionId)->first();
    }
    
    private function deleteMechanicPageInstruction($mechanicPageInstruction)
    {
        $mechanicPageInstruction->delete();
    }
    
    private function deleteMechanicInstruction($instructionId)
    {
        MechanicInstruction::destroy($instructionId);
    }
    
    private function errorResponse(string $message, int $statusCode = 500)
    {
        return response()->json(['error' => $message], $statusCode);
    }
}
