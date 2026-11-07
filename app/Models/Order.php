<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'total_price',
        'status',
        'estimated_completion_time',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function deliveries() { return $this->hasMany(Delivery::class); }
    public function delivery() { return $this->hasOne(Delivery::class); }

    public function getCustomerDisplayNameAttribute(): string
    {
        if ($this->customer && !empty($this->customer->name)) {
            return $this->customer->name;
        }
        return $this->customer_name ?: 'Pelanggan Walk-in';
    }

    public function getCustomerDisplayPhoneAttribute(): string
    {
        if ($this->customer && !empty($this->customer->phone)) {
            return $this->customer->phone;
        }
        return $this->customer_phone ?: '-';
    }
}
