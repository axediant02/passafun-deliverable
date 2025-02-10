<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $table = 'themes';

    protected $fillable = [
        'name',
        'main_color',
        'accent_color',
        'text_color',
        'button_color',
        'background_type',
        'background_value'
    ];

    public function getBackgroundValueUrlAttribute()
    {
        if ($this->background_type === 'image' && !empty($this->background_value)) {
            return Storage::disk('s3')->temporaryUrl($this->background_value, Carbon::now()->addHour());
        }
        return null;
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
