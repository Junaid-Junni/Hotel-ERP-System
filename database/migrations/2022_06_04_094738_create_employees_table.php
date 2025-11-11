<?php
// database/migrations/2024_01_01_000003_create_employees_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('country')->default('USA');
            $table->string('position');
            $table->string('department');
            $table->decimal('salary', 10, 2);
            $table->date('hire_date');
            $table->enum('employment_type', ['Full Time', 'Part Time', 'Contract', 'Temporary']);
            $table->enum('status', ['Active', 'Inactive', 'Suspended', 'Terminated'])->default('Active');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->text('emergency_contact_relation');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('profile_image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}