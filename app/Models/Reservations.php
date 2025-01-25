<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservations extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'status',
        'total_amount',
        'expires_at',
    ];

    /**
     * Bir rezervasyonun rezervasyon öğeleriyle ilişkisi.
     */
    public function items()
    {
        return $this->hasMany(Reservation_items::class, 'reservation_id');
    }

    /**
     * Bir rezervasyonun ilişkili etkinliği.
     */
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Rezervasyonu yapan kullanıcıyla ilişki.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
