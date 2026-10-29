<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AtkShoppingPlanDetail extends Model
{
    use HasFactory;

    protected $table = 'atk_shopping_plan_details';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'atk_shopping_plan_id',
        'atk_item_id',
        'jumlah_rencana',
        'jumlah_realisasi',
        'estimasi_harga',
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

    // Relasi ke Header Rencana Belanja
    public function shoppingPlan()
    {
        return $this->belongsTo(AtkShoppingPlan::class, 'atk_shopping_plan_id');
    }

    // Relasi ke Master Barang ATK
    public function item()
    {
        return $this->belongsTo(AtkItem::class, 'atk_item_id');
    }
}