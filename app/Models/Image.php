<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
