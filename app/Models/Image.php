<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\App;

class Image extends Model
{
    //conexión con la tabla de la base de datos
    protected $table = 'images';

    // campos que se pueden rellenar
    protected $fillable = [
        'image_path',
        'description',
    ];

    // campos que no se pueden mostrar
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    // function cast para cambiar el formato de la fecha
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:00',
            'updated_at' => 'datetime:Y-m-d H:00',
        ];
    }

    // ORM
    // relacion de muchos a uno (N imagenes pertenecen 1 usuario)
    public function user() {
        return $this->belongsToMany(User::class, 'user_id');
    }

    // relación de uno a muchos (1 imagen tiene N comentarios)
    public function comments() {
        return $this->hasMany(Comment::class)->orderBy('id', 'desc');
    }

    // relación de uno a muchos (1 imagen tiene N likes)
    public function likes() {
        return $this->hasMany(Like::class);
    }
}
