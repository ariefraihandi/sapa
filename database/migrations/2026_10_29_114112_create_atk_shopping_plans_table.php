<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atk_shopping_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');

            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->string('kode_rencana'); // Misal: RB-202610-001
            $table->string('nama_kegiatan'); // Misal: "Pengadaan ATK Perkara Triwulan IV"
            $table->date('tanggal_rencana');
            
            $table->enum('status', ['draft', 'approved', 'completed', 'cancelled'])->default('draft');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk_shopping_plans');
    }
};