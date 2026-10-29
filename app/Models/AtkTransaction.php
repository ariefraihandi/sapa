<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AtkTransaction extends Model
{
    use HasFactory;

    protected $table = 'atk_transactions';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'satker_id',
        'atk_item_id',
        'user_id',
        'nomor_dokumen',
        'jenis_transaksi',
        'jumlah',
        'nama_pengambil',
        'ruangan',
        'keterangan',
        'tanggal_transaksi',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
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

    // Relasi ke Master Barang
    public function item()
    {
        return $this->belongsTo(AtkItem::class, 'atk_item_id');
    }

    // Relasi ke User (Petugas Penginput)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}