<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atk_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke Satker (MS Aceh / Satker Daerah)
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');

            $table->string('kode_barang')->nullable(); // Misal: ATK-001
            $table->string('nama_barang');             // Misal: Kertas HVS A4
            $table->string('satuan', 50);              // rim, pcs, kotak, dll.
            
            // Pengelolaan Stok
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_saat_ini')->default(0);
            $table->integer('stok_minimal')->default(5); // Alert jika stok <= stok_minimal

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk_items');
    }
};