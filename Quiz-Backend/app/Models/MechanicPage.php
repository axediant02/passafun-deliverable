<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicPage extends Model
{
    use HasFactory;

    protected $table = 'mechanic_page';

    protected $fillable = [
        'background_image',
        'header',
        'button_text'
    ];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function mechanicPageInstructions()
    {
        return $this->hasMany(MechanicPageInstruction::class);
    }

    public function mechanicInstructions()
    {
        return $this->hasManyThrough(
            MechanicInstruction::class,     // The final model you want to retrieve
            MechanicPageInstruction::class, // The intermediate model
            'mechanic_page_id',             // Foreign key on the intermediate model
            'id',                           // Foreign key on the final model (MechanicInstruction)
            'id',                           // Local key on the MechanicPage model
            'instruction_id'                // Local key on the MechanicPageInstruction model
        );
    }
    
}
