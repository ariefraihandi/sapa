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
        Schema::create('notifikasi_ptsps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('satker_id');
            $table->boolean('is_pengunjung');
            $table->boolean('is_pengaduan');
            $table->timestamps();

            // Foreign key jika terhubung ke tabel satker (opsional)
            // $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi_ptsps');
    }
};