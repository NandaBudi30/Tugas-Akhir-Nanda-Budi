<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AksesGedung extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'no_kartu',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

