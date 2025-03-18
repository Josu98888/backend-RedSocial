<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request; //paquete para recoger los datos por solicitud
use Illuminate\Support\Facades\Validator;  //paquete para validar lo que llega 
use App\Models\User; //modelo del usuario
use Illuminate\Support\Facades\Hash; // paquete para cifrar la contraseña

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
}
