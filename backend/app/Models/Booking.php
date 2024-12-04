<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'guest_id',
        'full_name',
        'event_date',
        'event_time',
        'guest_count',
        'venue_name',
        'email',
        'phone',
        'special_requests',
        'payment_method',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
