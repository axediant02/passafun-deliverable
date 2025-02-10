<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $table = 'results';

    protected $fillable = [
        'quiz_id',
        'header',
        'description',
        'financial_tips',
        'image',
        'min_points',
        'max_points'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function result()
    {
        return $this->hasMany(Result::class, 'result_id');
    }
}