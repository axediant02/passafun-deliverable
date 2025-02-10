<?php

namespace App\DataTransfer;

class ResultPageData
{
    public ?string $header;
    public ?string $buttonText;
    public array $inputForms;
    public ?string $fileType;
    public $image;
    public $lottieJson;
    public $backgroundImage;

    public static function fromValidatedData(array $validatedData): self
    {
        $resultPage = new self();
        $resultPage->header = $validatedData['getResultHeader'] ?? null;
        $resultPage->buttonText = $validatedData['getResultButtonText'] ?? null;
        $resultPage->inputForms = $validatedData['getResultInputForms'] ?? [];
        $resultPage->fileType = $validatedData['getResultFileType'] ?? null;
        $resultPage->image = $validatedData['getResultImage'] ?? null;
        $resultPage->lottieJson = $validatedData['getResultLottieJson'] ?? null;
        $resultPage->backgroundImage = $validatedData['getResultBackgroundImage'] ?? null;
        
        return $resultPage;
    }

    public function toArray(): array
    {
        return [
            'header' => $this->header,
            'button_text' => $this->buttonText,
            'input_form' => $this->inputForms,
            'file_type' => $this->fileType,
            'image' => $this->image,
            'lottie_json' => $this->lottieJson,
            'background_image' => $this->backgroundImage,
        ];
    }
}