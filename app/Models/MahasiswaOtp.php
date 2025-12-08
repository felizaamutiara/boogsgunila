<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahasiswaOtp extends Model
{
    protected $table = 'mahasiswa_otps';

    protected $fillable = [
        'email',
        'otp',
        'expired_at',
    ];

    public $timestamps = true;
}
