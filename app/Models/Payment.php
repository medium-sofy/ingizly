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
        // Add other fillable attributes if needed
    ];

    /**
     * Get the order associated with the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
