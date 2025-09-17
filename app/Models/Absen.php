<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
