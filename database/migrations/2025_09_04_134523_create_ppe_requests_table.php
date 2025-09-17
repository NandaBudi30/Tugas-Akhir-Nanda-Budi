<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ppe_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_barang');
            $table->string('foto_barang'); // bisa simpan banyak foto
            $table->string('status')->default('pending'); // default pending
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppe_requests');
    }
};
