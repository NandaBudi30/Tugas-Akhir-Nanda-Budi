<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ppe_requests', function (Blueprint $table) {
            $table->json('foto_barang')->change();
        });
    }

    public function down(): void
    {
        Schema::table('ppe_requests', function (Blueprint $table) {
            $table->string('foto_barang')->change();
        });
    }
};
