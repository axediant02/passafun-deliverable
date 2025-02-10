<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageType extends Model
{
    protected $table = 'image_types';
    protected $fillable = ['name', 'description'];

    public function getResultPages()
    {
        return $this->hasMany(GetResultPage::class, 'image_type_id');
    }
}
