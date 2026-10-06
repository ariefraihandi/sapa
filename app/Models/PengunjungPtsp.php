<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengunjungPtsp extends Model
{
    use HasFactory;

    protected $table = 'pengunjung_ptsp';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'satker_id',
        'satker_tujuan_id',
        'jenis_layanan',
        'nama_responden',
        'nik',
        'no_hp',
        'email',
        'jenis_kelamin',
        'usia',
        'pekerjaan',
        'pendidikan',
        'keperluan',
        'is_tindak_lanjut',
        'tindak_lanjut_at',      // Tambahan kolom waktu tindak lanjut
        'durasi_tindak_lanjut',  // Tambahan kolom durasi (dalam detik)
    ];

    protected $casts = [
        'is_tindak_lanjut' => 'boolean',
        'tindak_lanjut_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Accessor untuk memformat durasi_tindak_lanjut (detik) ke teks yang mudah dibaca.
     * Contoh penggunaan di Blade: {{ $item->format_durasi }}
     */
    public function getFormatDurasiAttribute(): string
    {
        if (!$this->durasi_tindak_lanjut) {
            return '-';
        }

        $detik = $this->durasi_tindak_lanjut;
        $hari = floor($detik / 86400);
        $jam = floor(($detik % 86400) / 3600);
        $menit = floor(($detik % 3600) / 60);
        $sisaDetik = $detik % 60;

        $hasil = [];
        if ($hari > 0) $hasil[] = "{$hari} Hari";
        if ($jam > 0) $hasil[] = "{$jam} Jam";
        if ($menit > 0) $hasil[] = "{$menit} Menit";
        if ($sisaDetik > 0 || empty($hasil)) $hasil[] = "{$sisaDetik} Detik";

        return implode(' ', $hasil);
    }

    /**
     * Relasi ke Satker Awal / Penanggung Jawab Utama
     */
    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id');
    }

    /**
     * Relasi ke Satker Tujuan (Jika dialihkan / salah alamat)
     */
    public function satkerTujuan()
    {
        return $this->belongsTo(Satker::class, 'satker_tujuan_id');
    }
}