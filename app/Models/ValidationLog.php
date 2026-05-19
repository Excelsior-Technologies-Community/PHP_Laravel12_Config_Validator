<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidationLog extends Model
{
    protected $fillable = [
        'config_key',
        'status',
        'message'
    ];
}