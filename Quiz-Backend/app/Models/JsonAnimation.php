<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JsonAnimation extends Model
{
    protected $table = 'json_animations';
    protected $fillable = ['filename', 'filepath'];

    public function getResultPages()
    {
        return $this->hasMany(GetResultPage::class, 'json_animation_id');
    }
}
