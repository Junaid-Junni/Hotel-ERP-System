<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousekeepingTasksTable extends Migration
{
    public function up()
    {
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('cleaning_date');
            $table->time('cleaning_time');
            $table->enum('cleaning_type', ['Daily', 'Checkout', 'Deep', 'Maintenance']);
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed', 'Cancelled'])->default('Scheduled');
            $table->text('tasks')->nullable()->comment('JSON array of cleaning tasks');
            $table->text('notes')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->decimal('cleaning_supplies_cost', 8, 2)->default(0);
            $table->text('issues_found')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('quality_rating')->nullable()->comment('1-5 rating');
            $table->text('supervisor_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['cleaning_date', 'status']);
            $table->index(['room_id', 'cleaning_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('housekeepings');
    }
}
