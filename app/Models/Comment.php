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

}
