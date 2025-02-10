<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicInstruction extends Model
{
    use HasFactory;

    protected $table = 'mechanic_instructions';

    protected $fillable = ['instruction', 'icon'];

    public $timestamps = false;

    public function mechanicPageInstructions()
    {
        return $this->hasMany(MechanicPageInstruction::class);

    }
}
