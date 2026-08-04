<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satkers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Nama Satker
            $table->string('satker_name');       // Contoh: Mahkamah Syar'iyah Lhokseumawe
            $table->string('satker_short_name'); // Contoh: MS Lhokseumawe
            $table->string('satker_vshort');     // Contoh: ms-lsm
            
            // Detail Kontak & Lokasi
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Identitas Visual / Asset Branding
            $table->string('logo')->default('logo.png');
            $table->string('kop_surat')->nullable(); // Path/file gambar Kop Surat (PNG)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satkers');
    }
};