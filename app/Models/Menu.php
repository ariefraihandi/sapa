<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'icon',
        'url',
        'is_dropdown',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_dropdown' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
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

    // Relasi ke Submenu
    public function submenus()
    {
        return $this->hasMany(Submenu::class, 'menu_id', 'id')->orderBy('order', 'asc');
    }

    // Relasi ke MenuAccess
    public function accesses()
    {
        return $this->hasMany(MenuAccess::class, 'menu_id', 'id');
    }
}