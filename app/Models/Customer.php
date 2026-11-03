<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'loyalty_points',
        'total_spent',
    ];

    /**
     * Default attribute values for Customer.
     */
    protected $attributes = [
        'loyalty_points' => 0,
        'total_spent' => 0.00,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
