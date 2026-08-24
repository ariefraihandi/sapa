<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syarat_perkaras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('satker_id');
            $table->uuid('jenis_perkara_id');

            // UBAH DARI json MENJADI string
            $table->string('syarat_dokumen'); 
            
            $table->string('url_dokumen')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false); // Status persetujuan MS Aceh
            $table->text('catatan_verifikasi')->nullable(); // Catatan penolakan / revisi jika ada
            $table->timestamps();

            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');
            $table->foreign('jenis_perkara_id')->references('id')->on('jenis_perkaras')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syarat_perkaras');
    }
};