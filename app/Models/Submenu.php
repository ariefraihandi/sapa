<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Submenu extends Model
{
    use HasFactory;

    protected $table = 'submenus';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'menu_id',
        'submenu',
        'url',
        'order',
    ];

    protected $casts = [
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

    // Relasi balik ke Menu utama
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    // Relasi ke SubmenuAccess
    public function accesses()
    {
        return $this->hasMany(SubmenuAccess::class, 'submenu_id', 'id');
    }
}