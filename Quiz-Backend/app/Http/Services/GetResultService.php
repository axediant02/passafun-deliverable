<?php

namespace App\Http\Services;

use App\DataTransfer\ResultPageData;
use App\Http\Controllers\GetResultPageController;

class GetResultService
{
    private $getResultPageController;

    public function __construct(GetResultPageController $getResultPageController)
    {
        $this->getResultPageController = $getResultPageController;
    }

    public function createGetResult(ResultPageData $resultPage, int $quizId): void
    {
        $resultPageData = [
            'header' => $resultPage->header,
            'button_text' => $resultPage->buttonText,
            'input_form' => $resultPage->inputForms,
            'file_type' => $resultPage->fileType,
            'image' => $resultPage->image,
            'lottieJson' => $resultPage->lottieJson,
            'backgroundImage' => $resultPage->backgroundImage
        ];

        $this->getResultPageController->storeGetResultForm($resultPageData, $quizId);
    }
}