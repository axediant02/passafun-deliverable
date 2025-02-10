<?php

namespace App\Http\Controllers;


use App\Http\Requests\Quiz\SearchQuizRequest;
use App\Models\Quiz;
use App\Enums\QuizStatuses;
use App\Enums\QuestionTypeEnums;
use App\Http\Requests\Quiz\StoreQuizRequest;
use App\Http\Services\QuizValidationService;
use App\Repositories\QuizRepository;
use App\DataTransfer\ResultPageData;
use App\Http\Services\GetResultService;
use Illuminate\Http\Request;
use App\Models\LandingPage;
use App\Models\MechanicPage;
use App\Models\MechanicInstruction;
use App\Models\MechanicPageInstruction;
use App\Models\Result;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;


class QuizController extends Controller
{
    private $quizRepository;
    private $getResultService;

    protected $imageService;
    protected $quizValidationService;

    public function __construct(
        ImageService $imageService,
        QuizRepository $quizRepository,
        QuizValidationService $quizValidationService,
        GetResultService $getResultService
    ) {
        $this->imageService = $imageService;
        $this->quizRepository = $quizRepository;
        $this->quizValidationService = $quizValidationService;
        $this->getResultService = $getResultService;
    }

    public function index()
    {
        $quizzes = Quiz::withCount('participantQuizSummaries')
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($quiz) {
                $quiz->thumbnailUrl = $this->getTemporaryImageUrl($quiz->thumbnail);
                $quiz->coverImageUrl = $this->getTemporaryImageUrl($quiz->cover_image);
                return $quiz;
            });

        return response()->json([
            'quizzes' => $quizzes,
            'total_quizzes' => $quizzes->count(),
            'total_participants' => $quizzes->sum('participant_quiz_summaries_count')
        ]);
    }

    public function getQuizByStatus(Request $request)
    {
        $quizzes = null;
        $status = $request->query('status');
        if($status === 'featured'){
            $quizzes = Quiz::with('quizStatus')
                ->withCount('participantQuizSummaries as participant_count')
                ->where('is_featured', 1)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $quizzes = Quiz::with('quizStatus')
                ->withCount('participantQuizSummaries as participant_count')
                ->where('quiz_status_id', $status)
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($quizzes->isEmpty()) {
            return response()->json(['message' => 'No quizzes found for the specified status.'], 404);
        }

        $quizzes->each(function ($quiz) {
            $quiz->thumbnailUrl = $this->getTemporaryImageUrl($quiz->thumbnail);
            $quiz->coverImageUrl = $this->getTemporaryImageUrl($quiz->cover_image);
        });

        return response()->json($quizzes);
    }

    public function show($id)
    {
        return $this->getQuizByIdentifier('id', $id);
    }

    public function showQuizParticipants($id)
    {
        return $this->getQuizParticipantsByIdentifier('id', $id);
    }

    private function getQuizParticipantsByIdentifier($field, $value)
    {

        $quiz = $this->quizRepository->getQuizSummaryRelations($field, $value);

        return response()->json($quiz);
    }

    public function getPublishedQuizzesByUid($uid)
    {
        return $this->getQuizByIdentifier('uid', $uid);
    }

    private function getQuizByIdentifier($field, $value)
    {
        $client = new Client();

        $quiz = $this->quizRepository->getQuizWithFullRelations($field, $value);

        $quiz->thumbnailUrl = $this->getTemporaryImageUrl($quiz->thumbnail);
        $quiz->coverImageUrl = $this->getTemporaryImageUrl($quiz->cover_image);
        $quiz->shareThumbnailImageUrl = $this->getTemporaryImageUrl($quiz->share_thumbnail_image);

        if ($quiz->landingPage) {
            $this->setLandingPageImageUrls($quiz->landingPage);
        }

        if ($quiz->getResultPage) {
            $getResult = $quiz->getResultPage->first();
            $getResult->getResultImageUrl = $this->getTemporaryImageUrl($getResult->get_result_page_image);
            $getResult->getResultBackgroundImageUrl = $this->getTemporaryImageUrl($getResult->background_image);
            $jsonAnimationUrl = $getResult->jsonAnimation ? $this->getTemporaryImageUrl($getResult->jsonAnimation->filepath) : null;

            if ($jsonAnimationUrl) {
                $retrieveJsonFile =  $client->get($jsonAnimationUrl);
                $jsonFileDecoding =  $retrieveJsonFile->getBody()->getContents();
                $getResult->getJsonAnimationData = $jsonFileDecoding;
            }
        }

        $this->setQuestionImageUrls($quiz->questions);

        return response()->json($quiz);
    }

    private function setQuestionImageUrls($questions)
    {
        foreach ($questions as $question) {
            $question->question_image_url = $question->question_image
                ? $this->getTemporaryImageUrl($question->question_image)
                : null;

            $this->setChoiceImageUrls($question->choices);
        }
    }

    private function setChoiceImageUrls($choices)
    {
        foreach ($choices as $choice) {
            $choice->choice_image_url = $choice->choice_image
                ? $this->getTemporaryImageUrl($choice->choice_image)
                : null;
        }
    }

    private function setLandingPageImageUrls($landingPage)
    {
        $landingPage->landingImageUrl = $this->getTemporaryImageUrl($landingPage->landing_page_image);
        $landingPage->landingBackgroundImageUrl = $this->getTemporaryImageUrl($landingPage->background_image);
    }

    public function getPublishedQuizzesNoRelations()
    {
        $publishedQuizzes = Quiz::where('quiz_status_id', QuizStatuses::Published)
            ->get()
            ->each(fn($quiz) => $quiz->thumbnailUrl = $this->getTemporaryImageUrl($quiz->thumbnail));

        return response()->json($publishedQuizzes);
    }

    public function store(StoreQuizRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $resultPageData = ResultPageData::fromValidatedData($validatedData);

            $quiz = $this->createQuiz($validatedData, $request);

            $this->getResultService->createGetResult($resultPageData, $quiz->id);

            return response()->json($quiz, 201);
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to create quiz'
            ], 500);
        }
    }

    public function updateQuiz(StoreQuizRequest $request, $id)
    {
        if (empty($request->all())) {
            return response()->json(['success' => false, 'message' => 'No data received for update.'], 400);
        }

        $quiz = Quiz::findOrFail($id);
        $validatedData = $request->validated();

        if (isset($validatedData['name']) && $validatedData['name'] !== $quiz->name) {
            $baseUid = Str::slug($validatedData['name']);
            $uid = $baseUid;
            $counter = 1;

            while (Quiz::where('uid', $uid)->where('id', '!=', $id)->exists()) {
                $uid = $baseUid . '-' . $counter;
                $counter++;
            }

            $validatedData['uid'] = $uid;
        }

        $this->updateQuizFields($quiz, $validatedData);

        if ($request->hasFile('thumbnail')) {
            $quiz->thumbnail = $this->imageService->storeImage($request->file('thumbnail'), 'quiz-thumbnails');
        }
        if ($request->hasFile('coverImage')) {
            $quiz->cover_image = $this->imageService->storeImage($request->file('coverImage'), 'quiz-cover-image');
        }
        if ($request->hasFile('shareThumbnailImage')) {
            $quiz->share_thumbnail_image = $this->imageService->storeImage($request->file('shareThumbnailImage'), 'quiz-custom-share-images');
        }
        if ($request->hasFile('landingImage')) {
            $quiz->landingPage->landing_page_image = $this->imageService->storeImage($request->file('landingImage'), 'landing-page');
            $quiz->landingPage->save();
        }
        if ($request->hasFile('landingBackgroundImage')) {
            $quiz->landingPage->background_image = $this->imageService->storeImage($request->file('landingBackgroundImage'), 'landing-page-background');
            $quiz->landingPage->save();
        }

        $quiz->save();

        return response()->json(['success' => true, 'message' => 'Quiz updated successfully.', 'data' => $validatedData]);
    }

    public function updateQuizStatus(Request $request, $id)
    {
        $quiz = Quiz::with([
            'landingPage',
            'mechanicPage',
            'questions',
            'getResultPage',
            'results'
        ])->findOrFail($id);

        $newStatus = $request->input('quiz_status_id');
        if (is_null($newStatus)) {
            return response()->json(['message' => 'Quiz status cannot be null'], 400);
        }

        if ($newStatus == QuizStatuses::Published) {
            $validationResult = $this->quizValidationService->validateQuizCompleteness($quiz);

            if (!$validationResult['isValid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot publish incomplete quiz',
                    'errors' => $validationResult['errors']
                ], 422);
            }
        }

    $quiz->update([
                'quiz_status_id' => $newStatus,
                'is_featured' => 0,
            ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'quiz' => $quiz
        ]);
    }

    public function searchQuizzes(SearchQuizRequest $request)
    {
        $validatedData = $request->validated();

        $searchedQuizzes = Quiz::search($validatedData['quiz'])
            ->query(function ($query) {
                return $query->with(['landingPage', 'questions.choices'])
                            ->whereIn('quiz_status_id', [QuizStatuses::Published]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $searchedQuizzes->getCollection()->transform(function ($quiz) {
            $quiz = $this->addQuizImagesUrl($quiz);

            if ($quiz->landingPage) {
                $quiz->sub_header = $quiz->landingPage->sub_header;
            }
            return $quiz;
        });

        return response()->json($searchedQuizzes);
    }

    public function searchQuizzesAsAdmin(SearchQuizRequest $request)
    {
        $validatedData = $request->validated();

        $searchedQuizzes = Quiz::search($validatedData['quiz'])
            ->query(function ($query) {
                return $query->withCount('participantQuizSummaries as participant_count');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $searchedQuizzes->getCollection()->transform(function ($quiz) {
            $quiz = $this->addQuizImagesUrl($quiz);

            return $quiz;
        });

        return response()->json($searchedQuizzes);
    }

    private function addQuizImagesUrl($quiz)
    {
        $quiz->thumbnail_url = $this->imageService->getTemporaryImageUrl($quiz->thumbnail);
        $quiz->cover_image_url = $this->imageService->getTemporaryImageUrl($quiz->cover_image);
        $quiz->share_thumbnail_image_url = $this->imageService->getTemporaryImageUrl($quiz->share_thumbnail_image);
        return $quiz;
    }

    public function updateIsFeaturedQuiz (Request $request, $id){
        $quiz = Quiz::findOrFail($id);
        $newIsFeaturedValue = $request->input('isFeatured');

        if ($newIsFeaturedValue && Quiz::where('is_featured', 1)->count() >= 10) {
            return response()->json(['message' => 'Cannot feature more than 10 quizzes.'], 400);
        }

        $quiz->is_featured = $newIsFeaturedValue;
        $quiz->save();
        return response()->json(['message' => 'is_featured value is now updated']);
    }

    private function createQuiz($validatedData, $request)
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('No admin found!');
        }

        $imagePaths = $this->storeQuizImages($request);

        $landingPage = LandingPage::create([
            'sub_header' => $validatedData['landingSubheader'],
        ]);

        $mechanicPage = $this->createMechanicPage($validatedData);

        $baseName = $validatedData['name'];
        $baseUid = Str::slug($baseName);
        $name = $baseName;
        $uid = $baseUid;
        $counter = 1;

        while (Quiz::where('name', $name)->orWhere('uid', $uid)->exists()) {
            $name = $baseName . ' ' . $counter;
            $uid = $baseUid . '-' . $counter;
            $counter++;
        }

        return Quiz::create([
            'name' => $name,
            'description' => $validatedData['description'],
            'thumbnail' => $imagePaths['thumbnail'] ?? null,
            'cover_image' => $imagePaths['coverImage'] ?? null,
            'quiz_status_id' => QuizStatuses::Unpublished,
            'theme_id' => $validatedData['selectedTheme'] ?? null,
            'landing_page_id' => $landingPage->id,
            'mechanic_page_id' => $mechanicPage->id,
            'admin_id' => $user->id,
            'uid' => $uid,
            'share_thumbnail_image' => $imagePaths['shareThumbnailImage'],
        ]);
    }


    private function createMechanicPage($validatedData)
    {
        $mechanicPage = MechanicPage::create();

        if (isset($validatedData['mechanicsInstruction']) && !empty($validatedData['mechanicsInstruction'])) {
            foreach ($validatedData['mechanicsInstruction'] as $instruction) {
                if (!empty($instruction)) {
                    $mechanicInstruction = MechanicInstruction::create(['instruction' => $instruction]);
                    MechanicPageInstruction::create(['mechanic_page_id' => $mechanicPage->id, 'instruction_id' => $mechanicInstruction->id]);
                }
            }
        }

        return $mechanicPage;
    }

    private function updateQuizFields($quiz, $validatedData)
    {
        DB::transaction(function () use ($quiz, $validatedData) {
            $this->updateBasicQuizFields($quiz, $validatedData);
            $this->updateLandingPageFields($quiz, $validatedData);
        });
    }

    private function updateBasicQuizFields($quiz, $validatedData)
    {
        if (isset($validatedData['name'])) {
            $quiz->{$this->mapFieldName('name')} = $validatedData['name'];
        }

        if (isset($validatedData['uid'])) {
            $quiz->uid = $validatedData['uid'];
        }

        if (array_key_exists('description', $validatedData)) {
            $quiz->{$this->mapFieldName('description')} = $validatedData['description'];
        }

        $quiz->save();
    }

    private function updateLandingPageFields($quiz, $validatedData)
    {
        $landingPage = $quiz->landingPage;
        if (!$landingPage) {
            return;
        }

        $landingFields = [
            'landingSubheader' => 'sub_header',
        ];

        foreach ($landingFields as $validatedField => $dbField) {
            if (array_key_exists($validatedField, $validatedData)) {
                $landingPage->{$dbField} = $validatedData[$validatedField];
            }
        }

        $landingPage->save();
    }

    private function mapFieldName($field)
    {
        $fieldMappings = [
            'name' => 'name',
            'description' => 'description',
        ];
        return $fieldMappings[$field] ?? $field;
    }

    private function storeQuizImages($request)
    {
        $images = [
            'thumbnail' => $this->storeImage($request, 'thumbnail', 'quiz-thumbnails'),
            'coverImage' => $this->storeImage($request, 'coverImage', 'quiz-cover-images'),
            'shareThumbnailImage' => $this->storeImage($request, 'shareThumbnailImage', 'quiz-custom-share-images'),
        ];

        return $images;
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

    public function showQuizByUid($uid)
    {
        $quiz = Quiz::with([
            'quizStatus',
            'theme',
            'landingPage',
            'mechanicPage',
        ])->withCount('participantQuizSummaries')
            ->where('uid', $uid)
            ->firstOrFail();

        $quiz->thumbnailUrl = $this->getTemporaryImageUrl($quiz->thumbnail);
        $quiz->shareThumbnailImageUrl = $this->getTemporaryImageUrl($quiz->share_thumbnail_image);
        if ($quiz->landingPage) {
            $this->setLandingPageImageUrls($quiz->landingPage);
        }
        $this->setQuestionImageUrls($quiz->questions);

        return view('quiz', [
            'quiz' => $quiz,
            'metaTitle' => $quiz->name,
            'metaDescription' => $quiz->description,
            'metaImage' => $quiz->shareThumbnailImageUrl ?? $quiz->thumbnailUrl,
            'uid' => $uid,
        ]);
    }

    public function updateQuizTheme(Request $request, $id)
    {

        $quiz = Quiz::with('theme')->findOrFail($id);
        $quiz->theme_id = $request->input('themeId');
        $quiz->save();
    }

    public function destroyQuizImages(Request $request, $id){
        $quiz = Quiz::findOrFail($id);
        $response = $this->imageService->destroyImages($request, $quiz);
        return response()->json($response);
    }

    public function checkQuizIfPublished(Request $request)
    {
        $quizId = $request->query('quiz_id');
        $quiz = Quiz::findOrFail($quizId);

        if ($quiz->quiz_status_id === QuizStatuses::Published) {
            return response()->json(['isPublished' => 'true']);
        } else {
            return response()->json(['isPublished' => 'false']);
        }
    }

    public function getQuizNames()
    {
        try {
            $quizzes = Quiz::select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function($quiz) {
                    return [
                        'title' => $quiz->name,
                        'value' => $quiz->id
                    ];
                });

            return response()->json($quizzes);
        } catch (\Exception $e) {
            \Log::error('Error fetching published quizzes:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch quizzes'], 500);
        }
    }
}
