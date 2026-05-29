<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'postal_code',
        'is_default'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}