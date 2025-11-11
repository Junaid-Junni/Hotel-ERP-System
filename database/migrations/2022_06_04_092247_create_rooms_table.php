<?php
// database/migrations/2024_01_01_000001_create_rooms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('RoomNo')->unique(); // Changed to integer for room numbers 1-29
            $table->enum('Floor', ['1st Floor', '2nd Floor', '3rd Floor', '4th Floor']);
            $table->enum('Type', ['Standard', 'Deluxe', 'Suite']);
            $table->decimal('Price', 10, 2);
            $table->enum('Capacity', [1, 2, 3, 4]); // Limited to 1-4 persons
            $table->enum('Status', ['Available', 'Occupied', 'Maintenance', 'Cleaning'])->default('Available');
            $table->text('Description')->nullable();

            // Amenities
            $table->boolean('AC')->default(false);
            $table->boolean('TV')->default(false);
            $table->boolean('WiFi')->default(false);
            $table->boolean('Geyser')->default(false);
            $table->boolean('Balcony')->default(false);
            $table->boolean('Intercom')->default(false);
            $table->boolean('RoomService')->default(false);
            $table->boolean('Minibar')->default(false);

            $table->json('Images')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
