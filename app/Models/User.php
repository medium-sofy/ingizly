<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the admin record associated with the user.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Get the service buyer record associated with the user.
     */
    public function serviceBuyer()
    {
        return $this->hasOne(ServiceBuyer::class);
    }

    /**
     * Get the service provider record associated with the user.
     */
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a service buyer.
     */
    public function isServiceBuyer()
    {
        return $this->role === 'service_buyer';
    }

    /**
     * Check if the user is a service provider.
     */
    public function isServiceProvider()
    {
        return $this->role === 'service_provider';
    }

 public function notifications()
{
    return $this->hasMany(Notification::class, 'user_id'); 
}
}
