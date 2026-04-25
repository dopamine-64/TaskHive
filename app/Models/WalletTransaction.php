<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'booking_id', 'points', 'amount', 'type', 'description'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function booking()
    {
        return $this->belongsTo(Tracking::class, 'booking_id');
    }
}