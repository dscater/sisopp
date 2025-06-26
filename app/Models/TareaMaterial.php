<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        "tarea_id",
        "material_id",
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
