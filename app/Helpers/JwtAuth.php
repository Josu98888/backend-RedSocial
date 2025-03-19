<?php

namespace App\Helpers;

use Firebase\JWT\JWT; //paquete para codificar y decodificar tokens
use Illuminate\Support\Facades\DB; //paquete para hacer consultas a la BD
use App\Models\User; //modelo User
use Firebase\JWT\Key; //clase Key

class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = 'esta-es-una-clave-super-secreta-99bb7766';   //se crea la llave
    }

    public function signup($email, $password, $getToken = null)   //el getToken se utliza para obtener el token o devolverlo
    {
        $user = User::where('email', $email)->first();            //asignamos el user identificado

        //si el usuario existe
        if ($user) {
            $token = [
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'lastname' => $user->lastname,
                'nick' => $user->nick,
                'role' => $user->role,
                'image' => $user->image,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60),
            ];

            $jwt = JWT::encode($token, $this->key,'HS256');         //se genera el token 
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));               // se codifica el token

            return is_null($getToken) ? $jwt : $decoded;
        }
        return [
            'status' => 'error',
            'message' => 'Login incorrecto',
            'user' => $user
        ];
    }
}
