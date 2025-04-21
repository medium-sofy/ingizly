<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'buyer_id',
        'status',
        'total_amount',
        'scheduled_date',
        'scheduled_time',
        'location',
        'special_instructions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the service associated with the order.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the buyer associated with the order.
     */
    public function buyer()
    {
        return $this->belongsTo(ServiceBuyer::class, 'buyer_id', 'user_id');
    }

    /**
     * Get the user through the buyer relationship.
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            ServiceBuyer::class,
            'user_id', // Foreign key on service_buyers table
            'id', // Foreign key on users table
            'buyer_id', // Local key on orders table
            'user_id' // Local key on service_buyers table
        );
    }

    /**
     * Get the payments for the order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
