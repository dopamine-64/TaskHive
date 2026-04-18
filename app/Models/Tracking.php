<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    protected $table = 'trackings';

    protected $fillable = [
        'service_id',
        'customer_id',
        'provider_id',
        'booking_date',
        'booking_time',
        'address',
        'duration',
        'status',
        'current_lat',
        'current_lng',
    ];
}