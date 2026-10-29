<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atk_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');

            $table->uuid('atk_item_id');
            $table->foreign('atk_item_id')->references('id')->on('atk_items')->onDelete('cascade');

            $table->uuid('user_id')->nullable(); // Petugas/Admin penginput
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Nomor Dokumen (Contoh: "01/2026" sesuai form fisik)
            $table->string('nomor_dokumen')->nullable();

            $table->enum('jenis_transaksi', ['masuk', 'keluar']);
            $table->integer('jumlah');
            
            // Kolom Khusus Transaksi Keluar (Sesuai Form Fisik di Gambar)
            $table->string('nama_pengambil')->nullable(); // Nama orang yang mengambil
            $table->string('ruangan')->nullable();        // Ruang (Kolom 4 di form)
            $table->text('keterangan')->nullable();      // Ket (Kolom 5 di form)
            
            $table->date('tanggal_transaksi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk_transactions');
    }
};