<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class DeliveryDate extends Model
{
    protected $fillable = ['date'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (DeliveryDate $deliveryDate) {
            $hasOrders = $deliveryDate->timeSlots()
                ->whereHas('orders')
                ->exists();

            if ($hasOrders) {
                throw ValidationException::withMessages([
                    'deliveryDate' => 'This delivery date cannot be deleted because one or more of its time slots have orders assigned.',
                ]);
            }
        });
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }
}
