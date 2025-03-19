<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


//paquete para recoger los datos por solicitud
use Illuminate\Support\Facades\Validator;  //paquete para validar lo que llega 
use App\Models\User; //modelo del usuario
use Illuminate\Support\Facades\Hash; // paquete para cifrar la contraseña
use App\Helpers\JwtAuth; //helper

class UserController extends Controller
{
    public function prueba() {
        return "Hola desde el controlador de usuarios";
    }

    public function register(Request $request) {
        //Recoge los datos que llegan por post desde la vista (en formato json)
        $json = $request->input('json', null);
        // Decodificamos el json para que php pueda leerlo
        $params = json_decode($json);
        // Decodificamos y lo convertimos en un array
        $params_array = json_decode($json, true);

        // validamos que los datos no esten vacios
        if(!empty($params) && !empty($params_array)) {
            //limpiamos los datos
            $params_array = array_map('trim', $params_array);
            //validamos los datos con el Validator
            $validate = Validator::make($params_array, [
                'name' => 'required|alpha',
                'lastname' => 'required|alpha',
                'nick' => 'required|alpha', 
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8'
            ]);

            // Si no hay fallas en la validacion
            if (!$validate->fails()) {
                //ciframos la contraseña
                $pwd = Hash::make($params->password);
                //creamos el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->lastname = $params_array['lastname'];
                $user->nick = $params_array['nick'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';
                $user->image = '';
                //guardamos el usuario
                $user->save();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                ];
            }  else {
                $data = [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                ];
            }

        } else {
            $data = [
                'status' => 'error',
                'code' => '400',
                'message' => 'Los datos enviados son incorrectos, estan vaciós.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
        $JwtAuth = new JwtAuth();                       // creo el helper   
        $json = $request->input('json', null);          // Recogemos los datos del formulario en formato JSON
        $params = json_decode($json);                   // se transforma en objeto php
        $params_array = json_decode($json, true);       // se transforma en array

        // Validamos los datos
        $validate = Validator::make($params_array, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8'
        ]);

        // Si la validación falla
        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Error, el usuario no se ha podido loguear.',
                'errors' => $validate->errors()
            ], 400);
        }

        // Buscar usuario en la base de datos
        $user = User::where('email', $params->email)->first();

        // Verificar si la contraseña es incorrecta
        if (!Hash::check($params->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'La contraseña es incorrecta.'
            ], 401);
        }

        // Intentamos realizar el login
        if (isset($params->email) && isset($params->password)) {
            $getToken = isset($params->getToken) ? $params->getToken : null;           // se verifica si se ha enviado el token 
            $signup = $JwtAuth->signup($params->email, $params->password, $getToken);  // Llamada a la función de autenticación para generar el token

            return response()->json($signup, 200);
        }

        // Si faltan las credenciales
        return response()->json([
            'status' => 'error',
            'code' => 400,
            'message' => 'Los datos proporcionados son incompletos.'
        ], 400);
    }
}
