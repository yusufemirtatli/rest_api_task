<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $fillable = [
        'name',
        'description',
        'venue_id',
        'start_date',
        'end_date',
        'status',
    ];

    // İlişkiler (örnek olarak venues tablosu ile ilişki)
    public function venue()
    {
        return $this->belongsTo(Venues::class);
    }
}
