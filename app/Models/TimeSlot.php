<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class TimeSlot extends Model
{
    protected $fillable = ['delivery_date_id', 'start_time', 'end_time'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (TimeSlot $timeSlot) {
            if ($timeSlot->orders()->exists()) {
                throw ValidationException::withMessages([
                    'timeSlot' => 'This time slot cannot be deleted because it has orders assigned to it.',
                ]);
            }
        });
    }

    public function deliveryDate()
    {
        return $this->belongsTo(DeliveryDate::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_slot_id');
    }
}
