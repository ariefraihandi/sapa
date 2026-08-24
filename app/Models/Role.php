<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'satker_id',
        'role_name',
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
        return $this->belongsTo(Satker::class, 'satker_id', 'id');
    }

    public function menuAccesses()
    {
        return $this->hasMany(MenuAccess::class, 'role_id', 'id');
    }

    public function submenuAccesses()
    {
        return $this->hasMany(SubmenuAccess::class, 'role_id', 'id');
    }
}