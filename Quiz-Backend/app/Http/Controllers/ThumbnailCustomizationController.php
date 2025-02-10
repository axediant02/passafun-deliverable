<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThumbnailCustomization;
use App\Http\Services\ImageService;

class ThumbnailCustomizationController extends Controller
{
    public function store(Request $request)
    {
    $rules = [
        'quiz_id' => 'nullable|exists:quizzes,id',
        'prefix_text' => 'nullable|string',
        'prefix_text_color' => 'nullable|string',
        'header_text_color' => 'nullable|string',
        'description_text_color' => 'nullable|string',
        'button_text' => 'nullable|string',
        'button_color' => 'nullable|string',
        'button_text_color' => 'nullable|string',
        'background_type' => 'required|in:color,image',
    ];


    if ($request->background_type === 'color') {
        $rules['background_value'] = 'nullable|string';
    } elseif ($request->background_type === 'image') {
        $rules['background_value'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'; 
    }

    $validatedData = $request->validate($rules);

    if ($request->background_type === 'image' && $request->hasFile('background_value')) {
        $validatedData['background_value'] = $this->storeImage($request, 'background_value', 'backgrounds');
    }

    $thumbnailCustomization = ThumbnailCustomization::create($validatedData);

    return response()->json($thumbnailCustomization, 201);
}

public function update(Request $request, string $quizId)
{
    $request->validate([
        'quiz_id' => 'nullable|exists:quizzes,id',
        'prefix_text' => 'nullable|string',
        'prefix_text_color' => 'nullable|string',
        'header_text_color' => 'nullable|string',
        'description_text_color' => 'nullable|string',
        'button_text' => 'nullable|string',
        'button_color' => 'nullable|string',
        'button_text_color' => 'nullable|string',
        'background_type' => 'required|in:color,image',
    ]);
    
    if ($request->input('background_type') === 'image') {
        $baseRules['background_value'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
    } else {
        $baseRules['background_value'] = 'nullable|string';
    }

    $request->validate($baseRules);

    $thumbnailCustomization = ThumbnailCustomization::where('quiz_id', $quizId)->firstOrFail();

    if ($request->background_type === 'image' && $request->hasFile('background_value')) {
        if ($thumbnailCustomization->background_type === 'image' && $thumbnailCustomization->background_value) {
            $this->imageService->delete($thumbnailCustomization->background_value);
        }
        $path = $this->imageService->storeImage($request->file('background_value'), 'backgrounds');
        $thumbnailCustomization->background_value = $path;
    } else {
        $thumbnailCustomization->background_value = $request->input('background_value');
    }

    $thumbnailCustomization->update([
        'quiz_id' => $request->input('quiz_id'),
        'prefix_text' => $request->input('prefix_text'),
        'prefix_text_color' => $request->input('prefix_text_color'),
        'header_text_color' => $request->input('header_text_color'),
        'description_text_color' => $request->input('description_text_color'),
        'button_text' => $request->input('button_text'),
        'button_color' => $request->input('button_color'),
        'button_text_color' => $request->input('button_text_color'),
        'background_type' => $request->input('background_type'), 
    ]);

    return response()->json($thumbnailCustomization, 200);
}

    public function show($id)
    {
        $thumbnailCustomization = ThumbnailCustomization::where('quiz_id', $id)->firstOrFail();

        if ($thumbnailCustomization->background_type === 'image' && $thumbnailCustomization->background_value) {
            $thumbnailCustomization->background_value = $this->getTemporaryImageUrl($thumbnailCustomization->background_value);
        }

        return response()->json($thumbnailCustomization, 200);
    }

    protected $imageService;

    public function __construct(
        ImageService $imageService,
    ) {
        $this->imageService = $imageService;
    }

    private function storeImage($request, $inputName, $folder)
    {
        if ($request->hasFile($inputName) && $request->file($inputName)->isValid()) {
            return $this->imageService->storeImage($request->file($inputName), $folder);
        }

        return null;
    }

    private function getTemporaryImageUrl($path)
    {
        return $this->imageService->getTemporaryImageUrl($path);
    }
}