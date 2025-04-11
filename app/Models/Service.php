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
        'images',
        'location',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'view_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getImagesAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = is_array($value) ? implode(',', $value) : $value;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }
}
