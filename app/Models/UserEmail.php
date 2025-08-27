<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmail extends Model
{
    protected $fillable = ['user_id', 'email', 'is_locked'];
}
