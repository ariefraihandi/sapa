<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ptsp_daerahs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke tabel Satker
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');

            // Data Penanggung Jawab & Layanan
            $table->string('nama_pj')->comment('Nama Penanggung Jawab PTSP');
            $table->string('no_hp_pj')->comment('Nomor HP / WhatsApp Penanggung Jawab');
            
            $table->boolean('has_whatsapp_service')->default(false)->comment('Apakah Satker Memiliki Nomor HP Layanan?');
            $table->string('no_wa_layanan')->nullable()->comment('Nomor WhatsApp Layanan PTSP Publik');
            $table->boolean('is_call_able')->default(false)->comment('Headset / Perangkat Audio Call Ready');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ptsp_daerahs');
    }
};