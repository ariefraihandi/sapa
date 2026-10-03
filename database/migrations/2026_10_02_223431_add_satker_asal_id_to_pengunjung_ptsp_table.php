<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengunjung_ptsp', function (Blueprint $table) {
            $table->uuid('satker_tujuan_id')
                  ->nullable()
                  ->after('satker_id');

            $table->foreign('satker_tujuan_id')
                  ->references('id')
                  ->on('satkers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pengunjung_ptsp', function (Blueprint $table) {
            $table->dropForeign(['satker_tujuan_id']);
            $table->dropColumn('satker_tujuan_id');
        });
    }
};