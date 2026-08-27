<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PtspDaerah extends Model
{
    protected $table = 'ptsp_daerahs';

    // Beritahu Eloquent bahwa Primary Key bertipe String (bukan Auto-Increment Integer)
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'satker_id',
        'nama_pj',
        'no_hp_pj',
        'has_whatsapp_service',
        'no_wa_layanan',
        'is_call_able',
    ];

    protected $casts = [
        'has_whatsapp_service' => 'boolean',
        'is_call_able'          => 'boolean',
    ];

    /**
     * Auto-generate UUID saat membuat data baru di Laravel 8
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi Balik ke Satker
     */
    public function satker(): BelongsTo
    {
        return $this->belongsTo(Satker::class, 'satker_id', 'id');
    }
}