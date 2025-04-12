<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'buyer_id',
        'order_id',
        'rating',
        'buyer_name',
        'comment'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function buyer()
    {
        return $this->belongsTo(ServiceBuyer::class, 'buyer_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
    }
}