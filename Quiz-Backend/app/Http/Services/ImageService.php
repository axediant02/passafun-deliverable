<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function storeImage($image, $folder)
    {
        return $image->store($folder, 's3');
    }

    public function getTemporaryImageUrl($path)
    {
        return $path ? Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60)) : null;
    }

    public function destroyImages($request, $quiz){
        
        if ($request->query('landingBackgroundImage') === null) {
            $quiz->landingPage->background_image = null;
            $quiz->landingPage->save();
        } 

        return response()->json('Image Removed Successfully');
    }

    public function delete($path)
{
    if ($path && Storage::disk('s3')->exists($path)) {
        return Storage::disk('s3')->delete($path);
    }
    return false;
}
}
