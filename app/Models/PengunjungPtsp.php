<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\WorkCalendarService;
use App\Models\JenisPerkara;
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
        'jenis_layanan', // 'pesan', 'telepon', 'tatap_muka'
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
        'tindak_lanjut_at',
        'durasi_tindak_lanjut',
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

    /* =========================================================================
     * ACCESSORS & HELPER ATTRIBUTES
     * ========================================================================= */

    /**
     * Accessor untuk mengecek apakah keperluan berupa ID Jenis Perkara (UUID) atau Teks Biasa
     */
    public function getNamaKeperluanAttribute(): string
    {
        $value = $this->keperluan;

        if (empty($value)) {
            return '-';
        }

        // Cek apakah isi kolom keperluan adalah format UUID atau ID Integer/Numeric
        if (Str::isUuid($value) || is_numeric($value)) {
            $jenisPerkara = JenisPerkara::find($value);
            if ($jenisPerkara && !empty($jenisPerkara->nama_layanan)) {
                // 💡 Menambahkan narasi standar sebelum nama jenis perkaranya
                return "Permohonan Informasi " . $jenisPerkara->nama_layanan;
            }
        }

        // Jika teks biasa (misal: "kasir", "pendaftaran perkara"), kembalikan nilai aslinya
        return $value;
    }

    /**
     * Formatter Durasi Tindak Lanjut
     */
    public function getFormatDurasiAttribute(): string
    {
        if (!$this->durasi_tindak_lanjut) {
            return '-';
        }

        return WorkCalendarService::formatSeconds($this->durasi_tindak_lanjut);
    }

    /**
     * Helper untuk Cek Offline / Tatap Muka
     */
    public function getIsOfflineAttribute(): bool
    {
        return $this->jenis_layanan === 'tatap_muka';
    }

    /**
     * Helper untuk Cek Online
     */
    public function getIsOnlineAttribute(): bool
    {
        return in_array($this->jenis_layanan, ['pesan', 'telepon']);
    }

    /* =========================================================================
     * RELATIONS
     * ========================================================================= */

    /**
     * Relasi ke Satker Awal / Penanggung Jawab Utama
     */
    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id');
    }

    /**
     * Relasi ke Satker Tujuan
     */
    public function satkerTujuan()
    {
        return $this->belongsTo(Satker::class, 'satker_tujuan_id');
    }
}