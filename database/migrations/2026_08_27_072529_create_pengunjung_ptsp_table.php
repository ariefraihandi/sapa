<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengunjung_ptsp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke Satker
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');

            // Jenis Akses Layanan PTSP
            $table->enum('jenis_layanan', ['pesan', 'telepon'])->default('pesan');

            // Data Responden (e-Survei Badilag)
            $table->string('nama_responden');
            $table->string('nik', 16)->nullable();
            $table->string('no_hp', 20);
            $table->string('email')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('usia', 30)->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->text('keperluan')->nullable();

            // Status Tindak Lanjut & Survei
            $table->boolean('is_tindak_lanjut')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengunjung_ptsp');
    }
};