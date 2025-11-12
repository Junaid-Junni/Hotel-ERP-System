<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'payment_number',
        'amount',
        'payment_method',
        'digital_type',
        'transaction_id',
        'screenshot_path',
        'notes',
        'status',
        'payment_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date'
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getPaymentMethodTextAttribute()
    {
        return ucfirst($this->payment_method);
    }

    public function getDigitalTypeTextAttribute()
    {
        if (!$this->digital_type) return null;

        $types = [
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'e_wallet' => 'E-Wallet'
        ];

        return $types[$this->digital_type] ?? $this->digital_type;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'refunded' => 'info'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCash($query)
    {
        return $query->where('payment_method', 'cash');
    }

    public function scopeDigital($query)
    {
        return $query->where('payment_method', 'digital');
    }
}
