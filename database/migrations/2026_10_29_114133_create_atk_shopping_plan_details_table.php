<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atk_shopping_plan_details', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('atk_shopping_plan_id');
            $table->foreign('atk_shopping_plan_id')->references('id')->on('atk_shopping_plans')->onDelete('cascade');

            $table->uuid('atk_item_id');
            $table->foreign('atk_item_id')->references('id')->on('atk_items')->onDelete('cascade');

            $table->integer('jumlah_rencana');
            $table->integer('jumlah_realisasi')->default(0);
            $table->decimal('estimasi_harga', 15, 2)->default(0)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk_shopping_plan_details');
    }
};