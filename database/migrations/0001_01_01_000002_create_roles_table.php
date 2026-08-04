<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke tabel satkers
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');
            
            $table->string('role_name'); // pimpinan, admin, staff, administrator
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};