<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaOperario extends Model
{
    use HasFactory;

    protected $fillable = [
        "tarea_id",
        "user_id",
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
