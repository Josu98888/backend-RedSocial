<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     * 
     */
    //campos que se pueden rellenar 
    protected $fillable = [
        'name',
        'lastname',
        'nick',
        'role',
        'image',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    // campos que no se pueden mostrar 
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ORM
    // Relación de uno a muchos (1 usuario tiene N imágenes)
    public function images() {
        return $this->hasMany(Image::class)->orderBy('id', 'desc');
    }

    // relacion de uno a muchos (1 usuario tiene N comentarios)
    public function comments() {
        return $this->hasMany(Comment::class)->orderBy('id', 'desc');
    }

    // relacion de uno a muchos (1 usuario tiene N likes)
    public function likes() {
        return $this->hasMany(Like::class);
    }

    // relacion de uno a muchos (1 usuario tiene N mensajes enviados)
    public function messagesSent() {
        return $this->hasMany(Message::class, 'sender_id')->orderBy('id', 'desc');
    }

    // relacion de uno a muchos (1 usuario tiene N mensajes recibidos)
    public function messagesReceived() {
        return $this->hasMany(Message::class, 'receiver_id')->orderBy('id', 'desc');
    }
}
