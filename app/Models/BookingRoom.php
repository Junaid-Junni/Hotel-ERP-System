<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    use HasFactory;

    protected $table = 'booking_room';

    protected $fillable = [
        'booking_id',
        'room_id',
        'room_price',
        'extra_charges'
    ];

    protected $casts = [
        'room_price' => 'decimal:2',
        'extra_charges' => 'decimal:2'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getSubtotalAttribute()
    {
        return ($this->room_price * $this->booking->nights) + $this->extra_charges;
    }
}
