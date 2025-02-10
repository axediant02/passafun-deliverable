<?php

namespace App\Http\Controllers;

use App\Http\Requests\Themes\StoreThemeRequest;
use App\Http\Requests\Themes\UpdateThemeRequest;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ThemeController extends Controller
{
    public function index()
    {
        return response()->json(Theme::all());
    }

    public function show($id)
    {
        $theme = Theme::findOrFail($id);
        $theme->background_value_url = $this->getBackgroundValueUrl($theme);

        return response()->json($theme);
    }

    public function store(StoreThemeRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->background_type === 'image' && $request->hasFile('background_value')) {
            $validatedData['background_value'] = $this->storeBackgroundImage($request->file('background_value'));
        }

        $theme = Theme::create($validatedData);
        return response()->json($theme, 201);
    }

    public function update(UpdateThemeRequest $request, $id)
    {
        $theme = Theme::findOrFail($id);
        $validatedData = $request->validated();

        if ($request->background_type === 'image' && $request->hasFile('background_value')) {
            $this->deleteBackgroundImage($theme->background_value);
            $validatedData['background_value'] = $this->storeBackgroundImage($request->file('background_value'));
        }

        $theme->update($validatedData);
        return response()->json($theme);
    }

    public function destroy($id)
    {
        $theme = Theme::findOrFail($id);
        
        $nextTheme = Theme::where('id', '!=', $id)
            ->orderBy('id')
            ->first();

        if (!$nextTheme) {
            return response()->json(['message' => 'Cannot delete last remaining theme'], 400);
        }

        $this->deleteBackgroundImage($theme->background_value);
        
        DB::table('quizzes')
            ->where('theme_id', $id)
            ->update(['theme_id' => $nextTheme->id]);

        $theme->delete();
        return response()->json(null, 204);
    }

    private function storeBackgroundImage($image)
    {
        $originalName = $image->getClientOriginalName();
        return $image->storeAs('themes', $originalName, [
            'disk' => 's3',
            'visibility' => 'public',
        ]);
    }

    private function deleteBackgroundImage($path)
    {
        if ($path && Storage::disk('s3')->exists($path)) {
            Storage::disk('s3')->delete($path);
        }
    }

    private function getBackgroundValueUrl($theme)
    {
        if ($theme->background_type === 'image' && !empty($theme->background_value)) {
            return Storage::disk('s3')->temporaryUrl($theme->background_value, now()->addMinutes(60));
        }
        return null;
    }


    public function search(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);
    
        $themes = Theme::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->name) . '%'])->get();
    
        if ($themes->isEmpty()) {
            $themes = [];
        }
    
        return response()->json($themes);
    }
}
