<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\WorkCalendarService;
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
        'jenis_layanan', // Sudah mencakup 'pesan', 'telepon', 'tatap_muka'
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

    public function getFormatDurasiAttribute(): string
    {
        if (!$this->durasi_tindak_lanjut) {
            return '-';
        }

        return WorkCalendarService::formatSeconds($this->durasi_tindak_lanjut);
    }

    /**
     * Helper untuk cek apakah pengunjung hadir secara Offline / Tatap Muka
     */
    public function getIsOfflineAttribute(): bool
    {
        return $this->jenis_layanan === 'tatap_muka';
    }

    /**
     * Helper untuk cek apakah pengunjung Online (via Widget/Aplikasi)
     */
    public function getIsOnlineAttribute(): bool
    {
        return in_array($this->jenis_layanan, ['pesan', 'telepon']);
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