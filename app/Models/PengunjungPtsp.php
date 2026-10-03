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
        'satker_tujuan_id', // Ditambahkan untuk penanganan salah alamat / disposisi
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
    ];

    protected $casts = [
        'is_tindak_lanjut' => 'boolean',
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