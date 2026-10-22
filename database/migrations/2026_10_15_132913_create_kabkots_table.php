<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKabkotsTable extends Migration
{
    public function up()
    {
        Schema::create('kabkots', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kabkot'); // Contoh: "Kota Banda Aceh", "Kabupaten Aceh Besar"
            
            // Relasi ke Satker yang berwenang
            $table->uuid('satker_id');
            $table->foreign('satker_id')->references('id')->on('satkers')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kabkots');
    }
}