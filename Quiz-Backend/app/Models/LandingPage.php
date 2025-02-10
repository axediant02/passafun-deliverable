<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $table = 'landing_page';

    protected $fillable = [
        'background_image',
        'landing_page_image',
        'header',
        'sub_header',
        'button_text'
    ];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
