<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seats extends Model
{
    protected $fillable = [
        'section',
        'row',
        'venue_id',
        'number',
        'price',
        'status',
    ];
}
