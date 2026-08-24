<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_perkaras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kategori'); // cth: Perkawinan, Kewarisan & Harta, Ekonomi Syariah
            $table->string('nama_layanan'); // cth: Cerai Gugat
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_perkaras');
    }
};