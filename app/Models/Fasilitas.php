<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';
    protected $fillable = [
        'nama',
        'deskripsi',
        'stok',
        'harga',
        'image',
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

    public function bookingItems()
    {
        return $this->hasMany(BookingFasilitas::class, 'fasilitas_id');
    }
}


