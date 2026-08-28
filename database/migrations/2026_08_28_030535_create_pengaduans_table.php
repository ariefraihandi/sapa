<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke Satker
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');

            // Data Pelapor & Pengaduan
            $table->string('nama_pelapor');
            $table->string('no_hp', 20);
            $table->string('nik', 16)->nullable();
            $table->text('uaraian_pengaduan');

            // Data Tindak Lanjut
            $table->boolean('is_tindak_lanjut')->default(false);
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->string('file_tindak_lanjut')->nullable();
            $table->timestamp('tgl_tindak_lanjut')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};