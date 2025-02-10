<?php

namespace App\Enums;

enum QuestionTypeEnums: int
{
    public const MultipleSelect = 1;
    public const SingleSelect = 2;
    public const TrueFalse = 3;
    public const OpenEnded = 4;
    public const RatingScale = 5;

    public function label(): string
    {
        return match ($this) {
            self::MultipleSelect => 'Multiple Select Choice',
            self::SingleSelect => 'Single Select Choice',
            self::TrueFalse => 'True/False',
            self::OpenEnded => 'Open Ended',
            self::RatingScale => 'Rating Scale',
            default => 'Unknown Question Type',
        };
    }

    public static function tryFromWithFallback(int $value): ?self
    {
        return self::tryFrom($value) ?? null; 
    }
}
