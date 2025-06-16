<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email', // Added for guest checkout
        'shipping_name',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'billing_name',
        'billing_address',
        'billing_city',
        'billing_postal_code',
        'billing_country',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
    ];

    /**
     * Get the items associated with the order.
     * Each order can have multiple order items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the user that placed the order.
     * This relationship is optional, as orders can be placed by guest users (user_id will be null).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class); // Eloquent handles nullable foreign keys automatically
    }
}
