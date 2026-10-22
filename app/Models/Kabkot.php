<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabkot extends Model
{
    protected $table = 'kabkots';

    protected $fillable = [
        'nama_kabkot',
        'satker_id',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id', 'id');
    }
}