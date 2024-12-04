<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'package_name',
        'package_image',
        'package_description',
        'package_type',
        'package_price',
        'package_inclusion',
        'status',
        'flag'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
