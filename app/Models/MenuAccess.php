<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MenuAccess extends Model
{
    use HasFactory;

    protected $table = 'menu_accesses';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'role_id',
        'menu_id',
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

    // Relasi balik ke Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}