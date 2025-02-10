<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GetResultPage extends Model
{
    protected $fillable = [
        'quiz_id', 'header', 'background_image', 'get_result_page_image', 'button_text', 'json_animation_id', 'image_type_id'
    ];

    public function inputForms()
    {
        return $this->hasMany(InputForm::class);
    }

    // Relationship with Quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Relationship with ImageType
    public function imageType()
    {
        return $this->belongsTo(ImageType::class, 'image_type_id'); // Define the relationship
    }

    // Relationship with JsonAnimation (if applicable)
    public function jsonAnimation()
    {
        return $this->belongsTo(JsonAnimation::class, 'json_animation_id'); // Define the relationship
    }
}
