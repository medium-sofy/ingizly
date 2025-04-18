<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Import HasMany
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        // 'images', // <-- REMOVED from fillable
        'location',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'view_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // REMOVED getImagesAttribute
    // REMOVED setImagesAttribute

    /**
     * Get all of the images for the Service.
     * Defines the one-to-many relationship with ServiceImage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(): HasMany
    {
        // Assumes App\Models\ServiceImage exists and service_images table uses 'service_id'
        return $this->hasMany(ServiceImage::class);
    }

    // --- Existing Relationships ---
    public function category(): BelongsTo // Added BelongsTo type hint
    {
        return $this->belongsTo(Category::class);
    }

    // Renamed for convention, added BelongsTo type hint
    public function provider(): BelongsTo
    {
        // Ensure App\Models\ServiceProvider exists
        // Foreign key 'provider_id' links to 'user_id' on service_providers table
        return $this->belongsTo(ServiceProvider::class, 'provider_id', 'user_id');
    }

    public function orders(): HasMany // Added HasMany type hint
    {
        // Ensure App\Models\Order exists
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany // Added HasMany type hint
    {
        // Ensure App\Models\Review exists
        return $this->hasMany(Review::class);
    }

    public function violations(): HasMany // Added HasMany type hint
    {
        // Ensure App\Models\Violation exists
        return $this->hasMany(Violation::class);
    }

    // In app/Models/Service.php
public function cancelledOrders()
{
    return $this->hasMany(Order::class)->where('status', 'cancelled');
}
}