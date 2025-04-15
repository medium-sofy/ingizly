<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EmailVerification extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'code', 'expires_at'];

    public function isExpired()
    {
        return Carbon::now()->gt($this->expires_at);
    }
}