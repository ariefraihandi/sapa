<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AtkItem extends Model
{
    use HasFactory;

    protected $table = 'atk_items';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'satker_id',
        'kode_barang',
        'nama_barang',
        'satuan',
        'stok_awal',
        'stok_saat_ini',
        'stok_minimal',
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

    // Relasi ke Satker
    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id');
    }

    // Relasi ke Transaksi Keluar/Masuk
    public function transactions()
    {
        return $this->hasMany(AtkTransaction::class, 'atk_item_id');
    }

    // Relasi ke Detail Rencana Belanja
    public function shoppingPlanDetails()
    {
        return $this->hasMany(AtkShoppingPlanDetail::class, 'atk_item_id');
    }
}