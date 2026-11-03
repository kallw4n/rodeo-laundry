<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name', 'price', 'description'];

    protected $appends = ['unit'];

    public function orderItems() { return $this->hasMany(OrderItem::class, 'service_id'); }

    public function getUnitAttribute(): string
    {
        return self::resolveUnit($this->name);
    }

    public static function resolveUnit(?string $name): string
    {
        $name = strtolower($name ?? '');
        if (str_contains($name, 'kiloan') || str_contains($name, 'cuci kering') || str_contains($name, 'setrika')) {
            return 'kg';
        }
        if (str_contains($name, 'sepatu')) {
            return 'psg';
        }
        if (str_contains($name, 'gorden')) {
            return 'pcs';
        }
        return 'pcs';
    }
}

