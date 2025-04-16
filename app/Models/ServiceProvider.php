<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'phone_number',
        'bio',
        'location',
        'business_name',
        'business_address',
        'avg_rating',
        'provider_type',
        'is_verified',
    ];

    protected $casts = [
        'avg_rating' => 'float',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'provider_id', 'user_id');
    }

    public function availability()
    {
        return $this->hasMany(Availability::class, 'provider_id', 'user_id');
    }
}
