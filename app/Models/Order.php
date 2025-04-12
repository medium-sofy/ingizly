<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

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

    protected $casts = [
        'total_amount' => 'decimal:2',
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function buyer()
    {
        return $this->belongsTo(ServiceBuyer::class, 'buyer_id', 'user_id');
    }

    // public function payment()
    // {
    //     return $this->hasOne(Payment::class);
    // }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
    // In app/Models/Order.php

public function isReviewable()
{
    return $this->status === 'completed' && !$this->review;
}

public function scopeReviewable($query)
{
    return $query->where('status', 'completed')
                ->whereDoesntHave('review');
}
}