<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputForm extends Model
{
    protected $fillable = [
        'get_result_page_id', 'type', 'label', 'is_required'
    ];

    public function getResultPage()
    {
        return $this->belongsTo(GetResultPage::class);
    }
}
