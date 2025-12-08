<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingFasilitas extends Model
{
    use HasFactory;

    protected $table = 'booking_fasilitas';
    protected $fillable = [
        'booking_id',
        'fasilitas_id',
        'jumlah',
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

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }
}


