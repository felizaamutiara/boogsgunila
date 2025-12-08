<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $fillable = [
        'user_id',
        'gedung_id',
        'event_name',
        'event_type',
        'capacity',
        'phone',
        'date',
        'end_date',
        'start_time',
        'end_time',
        'proposal_file',
        'status',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }

    public function bookingFasilitas()
    {
        return $this->hasMany(BookingFasilitas::class);
    }

    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class, 'booking_id');
    }
}


