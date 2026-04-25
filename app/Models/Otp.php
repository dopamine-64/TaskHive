<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otps';
    
    protected $fillable = [
        'phone', 'code', 'type', 'data', 'expires_at', 'is_used'
    ];
    
    protected $casts = [
        'data' => 'array',
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];
}