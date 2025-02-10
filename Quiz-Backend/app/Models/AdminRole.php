<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    protected $table = 'admin_roles';
    protected $fillable = ['role'];

    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id');
    }
}
