<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizLibraryController;


Route::get('/library/popular-quizzes', [QuizLibraryController::class, 'getPopularQuizzes']);
Route::get('/library/published-quizzes', [QuizLibraryController::class, 'getPublishedQuizzes']);
Route::get('/library/featured-quizzes', [QuizLibraryController::class, 'getFeaturedQuizzes']);
