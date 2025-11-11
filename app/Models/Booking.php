<?php
// app/Models/Booking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_address',
        'adults',
        'children',
        'check_in',
        'check_out',
        'total_nights',
        'total_amount',
        'paid_amount',
        'payment_status',
        'status',
        'special_requests',
        'cancellation_reason'
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Calculate total nights
    public function calculateTotalNights()
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    // Calculate total amount
    public function calculateTotalAmount()
    {
        $room = $this->room;
        $nights = $this->calculateTotalNights();
        return $room->Price * $nights;
    }

    // Check if room is available for booking
    public static function isRoomAvailable($roomId, $checkIn, $checkOut, $bookingId = null)
    {
        $query = self::where('room_id', $roomId)
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->whereIn('status', ['Confirmed', 'Checked In']);

        if ($bookingId) {
            $query->where('id', '!=', $bookingId);
        }

        return $query->count() === 0;
    }
}
