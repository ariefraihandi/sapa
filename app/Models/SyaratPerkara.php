<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SyaratPerkara extends Model
{
    use HasFactory;

    protected $table = 'syarat_perkaras';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'satker_id',
        'jenis_perkara_id',
        'syarat_dokumen',
        'url_dokumen',
        'is_active',
        'is_approved',
        'catatan_verifikasi',
    ];

    protected $casts = [
        // 'syarat_dokumen' => 'array', // <--- SUDAH DIHAPUS
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
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
        return $this->belongsTo(Satker::class, 'satker_id', 'id');
    }

    public function jenisPerkara()
    {
        return $this->belongsTo(JenisPerkara::class, 'jenis_perkara_id', 'id');
    }
}