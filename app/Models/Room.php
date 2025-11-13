<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'RoomNo',
        'Floor',
        'Type',
        'Price',
        'Capacity',
        'Status',
        'Description',
        'AC',
        'WiFi',
        'TV',
        'Geyser',
        'Balcony',
        'Intercom',
        'RoomService',
        'Minibar',
        'Images'
    ];

    protected $casts = [
        'AC' => 'boolean',
        'WiFi' => 'boolean',
        'TV' => 'boolean',
        'Geyser' => 'boolean',
        'Balcony' => 'boolean',
        'Intercom' => 'boolean',
        'RoomService' => 'boolean',
        'Minibar' => 'boolean',
        'Images' => 'array',
        'Price' => 'decimal:2'
    ];

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->Price, 2);
    }

    public function getAmenitiesAttribute()
    {
        $amenities = [];

        $amenityMap = [
            'AC' => 'Air Conditioning',
            'TV' => 'TV',
            'WiFi' => 'WiFi',
            'Geyser' => 'Geyser',
            'Balcony' => 'Balcony',
            'Intercom' => 'Intercom',
            'RoomService' => 'Room Service',
            'Minibar' => 'Minibar'
        ];

        foreach ($amenityMap as $key => $value) {
            if ($this->$key) {
                $amenities[] = $value;
            }
        }

        return $amenities;
    }
}
