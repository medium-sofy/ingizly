<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'image_url', // matches your schema
        'is_primary'
    ];

    public $timestamps = true; // matches your created_at/updated_at columns

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->attributes['image_url'])) {
            return asset('images/default-service.jpg');
        }
        
        // Simplify the logic to directly use the asset helper
        return asset('storage/services/images/' . $this->attributes['image_url']);
    }
}