<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'admins';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id', 
    ];

    protected $hidden = ['password'];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

 
    public function admin_roles()
    {
        return $this->belongsTo(AdminRole::class, 'role_id')->select('id', 'role');
    }

}
