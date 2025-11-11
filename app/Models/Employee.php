<?php
// app/Models/Employee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'position',
        'department',
        'salary',
        'hire_date',
        'employment_type',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'bank_name',
        'account_number',
        'routing_number',
        'notes',
        'profile_image'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Generate employee ID
    public static function generateEmployeeId()
    {
        $latest = self::latest()->first();
        $number = $latest ? intval(substr($latest->employee_id, 3)) + 1 : 1;
        return 'EMP' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
