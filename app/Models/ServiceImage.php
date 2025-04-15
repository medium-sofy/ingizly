<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    use HasFactory;

    protected $fillable = ['image_url', 'is_primary', 'service_id'];

    //  the relationship with Service
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
