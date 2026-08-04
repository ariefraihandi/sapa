<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Satker extends Model
{
    use HasFactory;

    protected $table = 'satkers';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'satker_name',
        'satker_short_name',
        'satker_vshort',
        'alamat',
        'telepon',
        'whatsapp',
        'email',
        'website',
        'logo',
        'kop_surat',
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
}