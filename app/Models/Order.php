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

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function buyer()
    {
        return $this->belongsTo(ServiceBuyer::class, 'buyer_id', 'user_id');
    }
}