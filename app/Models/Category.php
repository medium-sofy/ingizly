<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'parent_category_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    public function childCategories()
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }

    // Alias relationships for better compatibility
    public function parent()
    {
        return $this->parentCategory();
    }

    public function children()
    {
        return $this->childCategories();
    }
}
