<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation_items extends Model
{
    protected $fillable = [
        'reservation_id',
        'seat_id',
        'price',
    ];

    /**
     * Bir rezervasyon öğesinin ilişkili rezervasyonuyla ilişkisi.
     */
    public function reservation()
    {
        return $this->belongsTo(Reservations::class, 'reservation_id');
    }

    /**
     * Bir rezervasyon öğesinin ilişkili koltuğuyla ilişkisi.
     */
    public function seat()
    {
        return $this->belongsTo(Seats::class, 'seat_id');
    }
}
