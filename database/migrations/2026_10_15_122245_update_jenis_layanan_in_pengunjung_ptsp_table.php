<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateJenisLayananInPengunjungPtspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE pengunjung_ptsp MODIFY COLUMN jenis_layanan ENUM('pesan', 'telepon', 'tatap_muka') DEFAULT 'pesan'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE pengunjung_ptsp MODIFY COLUMN jenis_layanan ENUM('pesan', 'telepon') DEFAULT 'pesan'");
    }
}