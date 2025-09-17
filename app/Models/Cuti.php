<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'alasan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_admin',
        'status_superadmin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
