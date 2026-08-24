<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JenisPerkara extends Model
{
    use HasFactory;

    protected $table = 'jenis_perkaras';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kategori',
        'nama_layanan',
        'deskripsi',
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

    // Relasi: Satu Jenis Perkara memiliki banyak Syarat Perkara (di berbagai satker)
    public function syaratPerkara()
    {
        return $this->hasMany(SyaratPerkara::class, 'jenis_perkara_id', 'id');
    }
}