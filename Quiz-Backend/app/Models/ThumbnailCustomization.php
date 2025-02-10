<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThumbnailCustomization extends Model
{
    use HasFactory;

    protected $table = 'thumbnail_customizations';

    protected $fillable = [
        'quiz_id',
        'prefix_text',
        'prefix_text_color',
        'header_text_color',
        'description_text_color',
        'button_text',
        'button_color',
        'button_text_color',
        'background_type',
        'background_value'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
}