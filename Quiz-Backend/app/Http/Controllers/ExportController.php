<?php

namespace App\Http\Controllers;


use App\Exports\AdminExport;
use App\Http\Requests\DataExtraction\ExportAllParticipantDetailsRequest;
use App\Http\Requests\DataExtraction\ExportParticipantSpecificQuizResponsesRequest;
use App\Http\Requests\DataExtraction\ExportSpecificParticipantDetailsRequest;
use App\Models\ParticipantQuizSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Exports\ParticipantQuizSummaryExport;
use App\Exports\ParticipantQuizSummaryByIdExport;
use App\Exports\QuizParticipantsExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ExportController extends Controller
{
    public function exportParticipantCsv(ExportAllParticipantDetailsRequest $request)
    {
        $validatedData = $request->validated();

        $selectedFields = $validatedData['selectedFields'];
        $dateRange = $validatedData['dateRange'] ?? [];
        $selectedQuizzes = $request->input('selectedQuizzes', []);

        $export = new ParticipantQuizSummaryExport(
            $selectedFields,
            $dateRange,
            null,
            null,
            null,
            $selectedQuizzes
        );

        return Excel::download($export, 'participants.csv');
    }

    public function exportParticipantCsvById(ExportSpecificParticipantDetailsRequest $request, $id)
    {
        $validatedData = $request->validated();

        $quizId = $request->input('quizId');
        $summaryId = $request->input('summaryId');
        $selectedQuizzes = $request->input('selectedQuizzes', []);
        $selectedFields = $validatedData['selectedFields'];
        $dateRange = $validatedData['dateRange'] ?? [];

        $participantId = (int) $id;
        $summaryId = (int) $summaryId;

        $participant = $this->getParticipant($id, $quizId, $summaryId, $dateRange);

        if (!$participant) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        return $this->exportCsv($selectedFields, $participantId, $quizId, $summaryId, $dateRange, $selectedQuizzes);
    }

    public function exportParticipantQuizSummaryCsvById(ExportParticipantSpecificQuizResponsesRequest $request, $participantId, $summaryId)
    {
        try {
            $validatedData = $request->validated();

            $quizId = $request->input('quizId');
            $selectedFields = $validatedData['selectedFields'];

            $summaryId = (int) $summaryId;
            $participantId = (int) $participantId;

            return $this->exportCsv($selectedFields, $participantId, $quizId, $summaryId);

        } catch (\Exception $e) {
            \Log::error('Error exporting participant quiz summary CSV: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function exportQuizParticipantsCsv(Request $request, $quizId)
    {
        try {
            $validatedData = $request->validate([
                'selectedFields' => 'required|array',
                'dateRange' => 'nullable|array'
            ]);

            $selectedFields = $validatedData['selectedFields'];
            $dateRange = $validatedData['dateRange'] ?? [];

            $export = new QuizParticipantsExport(
                $selectedFields,
                $quizId,
                $dateRange
            );

            return Excel::download($export, 'quiz-participants.csv');

        } catch (\Exception $e) {
            \Log::error('Error exporting quiz participants CSV: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    private function getParticipant($id, $quizId, $summaryId, $dateRange = null)
    {

        $ids = array_map('intval', explode(',', $id));

        if (empty($ids)) {
            return null;
        }

        $query = ParticipantQuizSummary::whereIn('participant_id', $ids)
            ->when($summaryId, fn($query) => $query->where('id', $summaryId))
            ->when($quizId, fn($query) => $query->where('quiz_id', $quizId));

        if (!empty($dateRange)) {
            if (isset($dateRange['start']) && isset($dateRange['end'])) {
                $startDate = Carbon::parse($dateRange['start'])->startOfDay();
                $endDate = Carbon::parse($dateRange['end'])->endOfDay();
                $query->whereBetween('completed_at', [$startDate, $endDate]);
            }
        }

        return $query->first();
    }

    private function getParticipantQuizSummary($participantId, $quizId, $summaryId, $dateRange = null)
    {

        $summaryId = (int) $summaryId;

        $query = ParticipantQuizSummary::where('id', $summaryId)
            ->when($quizId, fn($query) => $query->where('quiz_id', $quizId));

        if (!empty($dateRange)) {
            if (isset($dateRange['start']) && isset($dateRange['end'])) {
                $startDate = Carbon::parse($dateRange['start'])->startOfDay();
                $endDate = Carbon::parse($dateRange['end'])->endOfDay();
                $query->whereBetween('completed_at', [$startDate, $endDate]);
            }
        }

        return $query->first();
    }

    private function exportCsv($selectedFields, $id, $quizId, $summaryId, $dateRange = [], $selectedQuizzes = [])
    {
        return Excel::download(
            new ParticipantQuizSummaryByIdExport($selectedFields, $id, $quizId, $summaryId, $dateRange, $selectedQuizzes),
            'participant.csv'
        );
    }

}

