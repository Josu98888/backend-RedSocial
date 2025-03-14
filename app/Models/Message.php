<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //conexión con la BD
    protected $table = 'messages';

    // campos que se pueden asignar
    protected $fillable = [
        'sender_id', 'receiver_id', 'content', 'is_read'
    ];
}
