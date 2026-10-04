<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = ['order_id', 'type', 'address', 'scheduled_date', 'status'];

    public function order() { return $this->belongsTo(Order::class); }
}
