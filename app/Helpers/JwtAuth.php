<?php

namespace App\Helpers;

use Firebase\JWT\JWT; //paquete para codificar y decodificar tokens
use App\Models\User; //modelo User
use DomainException;
use Firebase\JWT\Key; //clase Key
use UnexpectedValueException;

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

    public function checkToken($jwt, $identity = false)
    {
        $auth = false;                                                                           // se inicializa en falso hasta qque se verifique

        try {
            $jwt = str_replace('"', '', $jwt);                                                   //se eliminan las comillas
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));                          //se decodifica el token
        } catch (UnexpectedValueException $e) {                                                  // se captura cualquier error y mantiene en falso $auth
            $auth = false;
        } catch (DomainException $e) {                                                           // se captura cualquier error y mantiene en falso $auth
            $auth = false;
        }

        // Verifica si la decodificación fue exitosa y si el token contiene un atributo 'sub'
        if (isset($decoded) && !empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if($identity != false) {          
            return $decoded ;                          // Si $identity es true retorna el usuario.
        } else {
            return $auth ;                             //Si $identity es false , solo retorna true o false dependiendo de la validez del token.
        }
    }
}
