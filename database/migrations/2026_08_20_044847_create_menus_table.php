<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('icon')->nullable()->comment('Contoh: bx bx-home atau fa-solid fa-gauge');
            $table->string('url')->nullable()->comment('Kosongkan jika is_dropdown true');
            $table->boolean('is_dropdown')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0)->comment('Urutan tampilan menu utama');            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
}