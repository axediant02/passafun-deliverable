<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicPageInstruction extends Model
{
    use HasFactory;

    protected $table = 'mechanic_page_instructions';

    protected $fillable = ['mechanic_page_id', 'instruction_id'];

    public $timestamps = false;

    public function mechanicPage()
    {
        return $this->belongsTo(MechanicPage::class, 'mechanic_page_id');
    }

    public function mechanicInstruction()
    {
        return $this->belongsTo(MechanicInstruction::class, 'instruction_id');
    }
}
