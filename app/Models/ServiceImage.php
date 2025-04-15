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
        'image_url', 
        'is_primary'
    ];

    public $timestamps = true; 

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->attributes['image_url'])) {
            return asset('images/default-service.jpg');
        }
        
        return asset('storage/services/images/' . $this->attributes['image_url']);
    }
}