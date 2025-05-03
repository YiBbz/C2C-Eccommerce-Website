<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'service_id',
        'customer_id',
        'provider_id',
        'booking_date',
        'end_date',
        'status',
        'total_amount',
        'special_instructions',
        'location',
        'latitude',
        'longitude',
        'is_paid',
        'payment_method',
        'payment_status'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'end_date' => 'datetime',
        'is_paid' => 'boolean',
        'total_amount' => 'decimal:2'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
