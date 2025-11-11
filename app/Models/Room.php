<?php
// app/Models/Room.php

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
        'TV',
        'WiFi',
        'Geyser',
        'Balcony',
        'Intercom',
        'RoomService',
        'Minibar',
        'Images'
    ];

    protected $casts = [
        'Price' => 'decimal:2',
        'AC' => 'boolean',
        'TV' => 'boolean',
        'WiFi' => 'boolean',
        'Geyser' => 'boolean',
        'Balcony' => 'boolean',
        'Intercom' => 'boolean',
        'RoomService' => 'boolean',
        'Minibar' => 'boolean',
        'Images' => 'array'
    ];

    protected $attributes = [
        'Status' => 'Available',
        'AC' => false,
        'TV' => false,
        'WiFi' => false,
        'Geyser' => false,
        'Balcony' => false,
        'Intercom' => false,
        'RoomService' => false,
        'Minibar' => false,
    ];
}
