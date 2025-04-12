<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'provider_id',
        'title',
        'description',
        'price',
        'view_count',
        'status',
        'service_type',
        'location'
    ];

    // Relationships matching your schema
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id', 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ServiceImage::class)->where('is_primary', true);
    }

    // Business logic methods
    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function totalReviews()
    {
        return $this->reviews()->count();
    }

    public function updateAverageRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $this->provider()->update(['avg_rating' => $avgRating]);
    }

    // Accessor for primary image URL
    public function getPrimaryImageUrlAttribute()
    {
        return $this->primaryImage ? $this->primaryImage->image_url : asset('images/default-service.jpg');
    }
}
