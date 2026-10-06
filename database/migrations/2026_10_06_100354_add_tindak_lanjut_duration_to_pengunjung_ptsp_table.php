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
        Schema::table('pengunjung_ptsp', function (Blueprint $table) {
            // Waktu eksak saat tombol tindak lanjut ditekan
            $table->timestamp('tindak_lanjut_at')->nullable()->after('is_tindak_lanjut');
            
            // Durasi selisih waktu dalam hitungan detik (created_at vs tindak_lanjut_at)
            $table->integer('durasi_tindak_lanjut')->nullable()->after('tindak_lanjut_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengunjung_ptsp', function (Blueprint $table) {
            $table->dropColumn(['tindak_lanjut_at', 'durasi_tindak_lanjut']);
        });
    }
};