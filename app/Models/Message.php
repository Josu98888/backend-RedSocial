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

    // ORM Eloquent
     // Relación con uno a uno (1 mensaje pertenece a un enviador)
     public function sender()
     {
         return $this->belongsTo(User::class, 'sender_id');
     }
 
     // Relación uno a uno (1 mensaje pertenece a un receptor)
     public function receiver()
     {
         return $this->belongsTo(User::class, 'receiver_id');
     }
}
