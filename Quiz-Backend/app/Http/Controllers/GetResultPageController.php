<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetResultPage\UpdateGetResultPageRequest;
use Illuminate\Http\Request;
use App\Models\GetResultPage;
use App\Models\JsonAnimation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class GetResultPageController extends Controller
{
    public function index()
    {
        return response()->json(GetResultPage::with('quiz')->get());
    }

    public function show($id)
    {
        $form = GetResultPage::with('inputForms')->findOrFail($id);
        $this->attachImageUrls([$form]);
        return response()->json($form);
    }

    public function getResultFormByQuizId($id)
    {
        $client = new Client();

        $getResult = GetResultPage::with(['inputForms:id,get_result_page_id,type,label,is_required', 'jsonAnimation'])
            ->where('quiz_id', $id)
            ->first();

        if ($getResult) {
            $getResult->getResultImageUrl = $this->getTemporaryFileUrl($getResult->get_result_page_image);
            $getResult->getResultBackgroundImageUrl = $this->getTemporaryFileUrl($getResult->background_image);
            $jsonAnimationUrl = $getResult->jsonAnimation ? $this->getTemporaryFileUrl($getResult->jsonAnimation->filepath) : null;

            if($jsonAnimationUrl){
                $retrieveJsonFile =  $client->get($jsonAnimationUrl);
                $jsonFileDecoding =  $retrieveJsonFile->getBody()->getContents();
                $getResult->getJsonAnimationData = $jsonFileDecoding;
            }
        }

        return response()->json($getResult);
    }


    public function storeGetResultForm(array $validatedData, int $quizId)
    {
        $form = $this->createGetResultPage($validatedData, $quizId);
        $this->saveInputForms($form, $validatedData['input_form'] ?? []);
        return $this->createSuccessResponse($form);
    }

    private function createGetResultPage(array $validatedData, int $quizId): GetResultPage
    {
        $form = new GetResultPage([
            'quiz_id' => $quizId,
            'header' => $validatedData['header'],
            'button_text' => $validatedData['button_text'],
        ]);
        
        if(isset($validatedData['image']) && $validatedData['file_type'] === 'image'){
            $form->get_result_page_image = $this->storeFile($validatedData['image'], 'get-result-images');
            $form->image_type_id = 1;
        }
        
        if(isset($validatedData['lottieJson']) && $validatedData['file_type'] === 'json'){
            $filepath = $this->storeFile($validatedData['lottieJson'], 'json-animation');
            $jsonAnimation = JsonAnimation::firstOrCreate(
                ['filename' => $validatedData['lottieJson']->getClientOriginalName()],
                ['filepath' => $filepath],
            );
            $form->image_type_id = 2;
            $form->json_animation_id = $jsonAnimation->id;
        }

        if(isset($validatedData['backgroundImage'])){
            $form->background_image = $this->storeFile($validatedData['backgroundImage'], 'get-result-background-images');
        }
        
        $form->save();
        return $form;
    }

    private function saveInputForms(GetResultPage $form, array $inputForms): void
    {
        foreach ($inputForms as $inputFormData) {
            $this->createInputForm($form, $inputFormData);
        }
    }
    private function createInputForm(GetResultPage $form, array $inputFormData): void
    {
        $form->inputForms()->create([
            'type' => $inputFormData['type'],
            'label' => $inputFormData['label'],
            'is_required' => $inputFormData['is_required'],
        ]);
    }
    private function createSuccessResponse(GetResultPage $form): JsonResponse
    {
        return response()->json([
            'message' => 'Participant form stored successfully!',
            'form' => $form->load('inputForms'),
        ], 201);
    }

    public function update(UpdateGetResultPageRequest $request, $id)
    {

        $getResult = GetResultPage::with(['inputForms:id,get_result_page_id,type,label,is_required', 'jsonAnimation'])
        ->where('quiz_id', $id)
        ->first();
        $validatedData = $request->validated();


        collect(['image' => 'get_result_page_image', 'backgroundImage' => 'background_image', 'jsonFile' => 'json_animation_id'])
            ->filter(fn($field, $key) => array_key_exists($key, $validatedData))
            ->each(function ($field, $key) use ($validatedData, $getResult) {

                if ($validatedData[$key] === null) {
                    if ($key === 'jsonFile') {
                        if ($getResult->json_animation_id) {
                            JsonAnimation::destroy($getResult->json_animation_id);
                        }
                    }
                    $getResult->$field = null;
                    if (in_array($key, ['image', 'jsonFile'])) {
                        $getResult->image_type_id = null; 
                    }
                } else {
                    if (in_array($key, ['image', 'backgroundImage'])) {
                        $getResult->$field = $this->handleImageUpload($validatedData);
                        if ($key === 'image') {
                            $getResult->image_type_id = 1;
                            $getResult->json_animation_id = null;
                        }
                    } elseif ($key === 'jsonFile') {
                        $file = $validatedData['jsonFile'];
                        $filePath = $this->storeFile($file, 'json-animation');
                        $jsonAnimation = JsonAnimation::firstOrCreate(
                            ['filename' => $file->getClientOriginalName()],
                            ['filepath' => $filePath],
                        );
                        $getResult->get_result_page_image = null;
                        $getResult->image_type_id = 2;
                        $getResult->json_animation_id = $jsonAnimation->id;
                    }
                }
            });


        $this->updateGetResultFields($getResult, $validatedData);
        if (isset($validatedData['inputForms'])) {
            $this->updateOrCreateInputForms($getResult, $validatedData['inputForms']);
        }
        $getResult->save();
        $getResult->load('inputForms', 'jsonAnimation');
        return response()->json($getResult);
    }

    private function handleImageUpload($validatedData)
    {
        if (isset($validatedData['image'])) {
            if ($validatedData['image'] === null) {
                return null;
            }
            return $this->storeFile($validatedData['image'], 'get-result-images');
        }

        if (isset($validatedData['backgroundImage'])) {
            if ($validatedData['backgroundImage'] === null) {
                return null;
            }
            return $this->storeFile($validatedData['backgroundImage'], 'get-result-background-images');
        }
    }

    private function updateGetResultFields($getResult, array $validatedData)
    {
        if (isset($validatedData['header'])) {
            $getResult->header = $validatedData['header'];
        }
        if (isset($validatedData['buttonText'])) {
            $getResult->button_text = $validatedData['buttonText'];
        }
    }

    private function updateOrCreateInputForms($getResult, array $inputForms)
    {
        foreach ($inputForms as $inputFormData) {
            $getResult->inputForms()->updateOrCreate(
                ['id' => $inputFormData['id']],
                [
                    'type' => $inputFormData['type'],
                    'label' => $inputFormData['label'],
                    'is_required' => $inputFormData['is_required'],
                    'get_result_page_id' => $inputFormData['get_result_page_id'],
                ]
            );
        }
    }



    public function destroy(Request $request, $resultId)
    {
        $formIds = $request->query('formIds'); 
        $getResult = GetResultPage::with('inputForms')->find($resultId);
        $deletedForms = [];

        foreach ($formIds as $formId) {
            $inputForm = $getResult->inputForms->firstWhere('id', $formId);
            if ($inputForm) {
                $deletedForms[] = $inputForm; 
                $inputForm->delete();
            }
        }

        return response()->json($deletedForms);
    }

    protected function storeFile($file, $folder)
    {
        $originalName = $file->getClientOriginalName();
        return $file->storeAs($folder, $originalName, [
            'disk' => 's3',
            'visibility' => 'private',
        ]);
    }

    private function getTemporaryFileUrl($path)
    {
        return $path ? Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60)) : null;
    }
}
