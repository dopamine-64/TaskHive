<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'subcategory',
        'price'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
