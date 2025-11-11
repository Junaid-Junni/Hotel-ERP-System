<?php
// app/Models/Inventory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory';
    protected $primaryKey = 'id';

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category',
        'brand',
        'cost_price',
        'selling_price',
        'quantity',
        'min_stock_level',
        'max_stock_level',
        'location',
        'supplier',
        'supplier_contact',
        'expiry_date',
        'barcode',
        'image',
        'status',
        'attributes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'expiry_date' => 'date',
        'attributes' => 'array'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= min_stock_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getStockStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->quantity <= $this->min_stock_level) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getProfitMarginAttribute()
    {
        if ($this->cost_price > 0) {
            return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
        }
        return 0;
    }

    public function getTotalValueAttribute()
    {
        return $this->cost_price * $this->quantity;
    }

    // Methods
    public function isLowStock()
    {
        return $this->quantity <= $this->min_stock_level;
    }

    public function isOutOfStock()
    {
        return $this->quantity <= 0;
    }

    // Generate SKU automatically
    public static function generateSKU($name, $category)
    {
        $baseSKU = strtoupper(substr($category, 0, 3)) . strtoupper(substr(preg_replace('/\s+/', '', $name), 0, 5));
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        return $baseSKU . $random;
    }
}
