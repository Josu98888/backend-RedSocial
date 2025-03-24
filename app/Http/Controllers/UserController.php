<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;                           //paquete para recoger los datos por solicitud
use Illuminate\Support\Facades\Validator;              //paquete para validar lo que llega 
use App\Models\User;                                   //modelo del usuario
use Illuminate\Support\Facades\Hash;                   // paquete para cifrar la contraseña
use App\Helpers\JwtAuth;                               //helper
use Illuminate\Support\Facades\Storage;                //paquete para el storage
use Illuminate\Support\Facades\File;                   //paquete para el archivo de imagen

class UserController extends Controller
{
    public function prueba()
    {
        return "Hola desde el controlador de usuarios";
    }

    public function register(Request $request)
    {
        //Recoge los datos que llegan por post desde la vista (en formato json)
        $json = $request->input('json', null);
        // Decodificamos el json para que php pueda leerlo
        $params = json_decode($json);
        // Decodificamos y lo convertimos en un array
        $params_array = json_decode($json, true);

        // validamos que los datos no esten vacios
        if (!empty($params) && !empty($params_array)) {
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
            } else {
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

    public function login(Request $request)
    {
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

    public function update(Request $request)
    {
        $token = $request->header('Authorization');                    //obtiene el token del encabezado de la solicitud

        $jwtAuth = new JwtAuth();                        
        $checkToken = $jwtAuth->checkToken($token);                    // se crea una instancia de JwtAuth y se verifica el token 
        $json = $request->input('json', null);                         // Recogemos los datos del formulario en formato JSON
        $params_array = json_decode($json, true);                      // creo un array con los datos y los decodifico

        // verifica si el token es válido
        if ($checkToken) {   
            $user = $jwtAuth->checkToken($token, true);                          // obtener el user identificado
            $id = $user->sub;                                                    // obtiene el ID del usuario desde el token
            $user = User::findOrFail($id);                                       // obiene el usuario en la base de datos desde el id

            $validate = Validator::make($params_array, [                         // valida los datos recibidos
                'name' => 'required',
                'lastname' => 'required',
                'nick' => 'required',
                'email' => 'email|unique:users,email,' . $user->id,
                'image' => 'image|mimes:jpg,png,jpeg,gif|max:2048'
            ]);

            if (!$validate->fails()) {
                $user->update($params_array);                                      // actualiza los datos del usuario (excepto la imagen)
                

                // si el user cambia la imagen
                if ($request->hasFile('image')) {
                    
                    if ($user->image) {                                  
                        Storage::disk('users')->delete($user->image);               // Elimina imagen anterior si existe
                    }

                    $image = $request->file('image');                               
                    $image_name = time() . '_' . $image->getClientOriginalName();   // Asigna un nombre único
                    Storage::disk('users')->put($image_name, File::get($image));    // Guarda nueva imagen
                    $user->image = $image_name;                                     // Guardar la ruta relativa en la base de datos
                }
                
                $user->save();                                                      // Guardar cambios en la base de datos

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'user' => $user,
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Error al ingresar los datos.'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no esta identificado.'
            ];
        }


        return response()->json($data, $data['code']);
    }
}
