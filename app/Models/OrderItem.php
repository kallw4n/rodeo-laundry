<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['order_id', 'service_id', 'quantity', 'price'];

    protected $casts = [
        'quantity' => 'float',
        'price'    => 'float',
    ];

    protected $appends = ['unit'];

    public function getUnitAttribute(): string
    {
        return $this->service ? ($this->service->unit ?? ServiceType::resolveUnit($this->service->name)) : 'kg';
    }

    public function order() { return $this->belongsTo(Order::class); }
    public function service() { return $this->belongsTo(ServiceType::class, 'service_id'); }
}
