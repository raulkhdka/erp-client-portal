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

    // Constants for user roles
    const ROLE_ADMIN = 'admin';
    const ROLE_CLIENT = 'client';
    const ROLE_EMPLOYEE = 'employee';

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is client
     */
    public function isClient()
    {
        return $this->role === self::ROLE_CLIENT;
    }

    /**
     * Check if user is employee
     */
    public function isEmployee()
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    /**
     * Get the client profile if user is a client
     */
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    /**
     * Get the employee profile if user is an employee
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
