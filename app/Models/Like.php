<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    //conexión con la tabla de la base de datos
    protected $table = 'likes';

    // ORM
    // Relación uno a uno (1 like pertenece 1 usuario)
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación uno a uno (1 like pertenece 1 imagen)
    public function image() {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
