<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //conexión con la tabla de la base de datos
    protected $table = 'comments';

    // campos que se pueden rellenar
    protected $fillable = [
        'content',
    ];

    // ORM
    // Relación muchos a uno (N comentarios pertenecen 1 usuario)
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación muchos a uno (N comentarios pertenecen 1 imagen)
    public function image() {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
