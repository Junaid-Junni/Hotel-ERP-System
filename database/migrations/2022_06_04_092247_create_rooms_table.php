<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('RoomNo')->unique();
            $table->integer('Floor');
            $table->string('Type');
            $table->decimal('Price', 10, 2);
            $table->integer('Capacity');
            $table->enum('Status', ['Available', 'Occupied', 'Maintenance', 'Cleaning'])->default('Available');
            $table->text('Description')->nullable();
            $table->boolean('AC')->default(false);
            $table->boolean('WiFi')->default(false);
            $table->boolean('TV')->default(false);
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
};
