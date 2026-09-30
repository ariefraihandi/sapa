<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NotifikasiPtsp extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_ptsps';

    // Set Primary Key menggunakan UUID
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'satker_id',
        'is_pengunjung',
        'is_pengaduan',
    ];

    protected $casts = [
        'is_pengunjung' => 'boolean',
        'is_pengaduan' => 'boolean',
    ];

    // Auto generate UUID saat record baru dibuat
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi ke Satker
    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id');
    }
}