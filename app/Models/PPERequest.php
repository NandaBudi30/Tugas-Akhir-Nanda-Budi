<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPERequest extends Model
{
    use HasFactory;

    // ✅ Tambahkan ini biar Laravel tahu tabel yang benar
    protected $table = 'ppe_requests';

    protected $fillable = [
        'user_id',
        'nama_barang',
        'foto_barang',
        'status',
    ];

    protected $casts = [
        'foto_barang' => 'array', // simpan multiple image sebagai array JSON
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
