<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['vendor_id', 'status', 'total', 'customer_name', 'customer_phone'];

    protected $casts = [
        'total' => 'decimal:2',
        'customer_name' => 'encrypted',
        'customer_phone' => 'encrypted',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
