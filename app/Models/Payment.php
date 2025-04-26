<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'payment_gateway',
        'amount',
        'currency',
        'payment_status',
        'transaction_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order associated with the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user through the order.
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Order::class,
            'id', // Foreign key on orders table
            'id', // Foreign key on users table
            'order_id', // Local key on payments table
            'buyer_id' // Local key on orders table
        );
    }

    /**
     * Get the service through the order.
     */
    public function service()
    {
        return $this->hasOneThrough(
            Service::class,
            Order::class,
            'id', // Foreign key on orders table
            'id', // Foreign key on services table
            'order_id', // Local key on payments table
            'service_id' // Local key on orders table
        );
    }
}
