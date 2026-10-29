<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AtkShoppingPlan extends Model
{
    use HasFactory;

    protected $table = 'atk_shopping_plans';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'satker_id',
        'created_by',
        'kode_rencana',
        'nama_kegiatan',
        'tanggal_rencana',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_rencana' => 'date',
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

    // Relasi ke User Pembuat Rencana
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke Detail Item Rencana Belanja
    public function details()
    {
        return $this->hasMany(AtkShoppingPlanDetail::class, 'atk_shopping_plan_id');
    }
}