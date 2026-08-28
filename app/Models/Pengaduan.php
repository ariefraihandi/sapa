<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduans';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'satker_id',
        'nama_pelapor',
        'no_hp',
        'nik',
        'uaraian_pengaduan',
        'is_tindak_lanjut',
        'catatan_tindak_lanjut',
        'file_tindak_lanjut',
        'tgl_tindak_lanjut',
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

    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id');
    }
}