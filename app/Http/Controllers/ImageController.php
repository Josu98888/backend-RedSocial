<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function store(Request $request) {
        $json = $request->input('json', null);                                           // Recogemos los datos del formulario en formato JSON
        $params_array = json_decode($json, true);                                        // Decodificamos el JSON en un array

        $validate = Validator::make($params_array, [                                     // Validamos los datos
            "image_path" => "image|mimes:jpg,jpeg,png,gif", 
            "description" => "required"
        ]);      
        
        if(!empty($params_array)) {    
            if(!$validate->fails()) {
                $user = $request->authUser;                                               // Obtener el usuario desde el middleware
                $image = new Image();                                                     // Crear una nueva instancia de Image
                $image->user_id = $user->id;                                              // Asignar el id del usuario al campo user_id   
                $image->description = $params_array["description"];                       // Asignar la descripción de la imagen
            
                if ($request->hasFile('image')) {
                    $image_path = $request->file('image');                               
                    $image_name = time() . '_' . $image_path->getClientOriginalName();    // Asigna un nombre único
                    Storage::disk('images')->put($image_name, File::get($image_path));    // Guarda nueva imagen
                    $image->image_path = $image_name;                                     // Guardar la ruta relativa en la base de datos
                }
                $image->save();                                                           // Guardar la imagen en la base de datos    

                $data = [
                    "status" => "success",
                    "code" => 200,
                    "image" => $image 
                ];
            } else {
                $data = [
                    "status" => "error",
                    "code" => 400,
                    "message" => "Error al enviar los datos, no se ha creado la imagen."
                ];
            }
        } else {
            $data = [
                "status" => "error",
                "code" => 400,
                "message" => "Error, no se han enviado los datos."
            ];
        }

        return response()->json($data, $data["code"]);                 // Devolvemos la respuesta en formato JSON
    }

    public function getImage($filename)
    {
            $path = storage_path("app/public/images/{$filename}");                    // Construye la ruta completa donde se encuentra la imagen en el almacenamiento.

            // Verifica si el archivo existe en la ruta especificada.
            if (File::exists($path)) {
                $file = File::get($path);                                            // Obtiene el contenido del archivo.
                $mimeType = File::mimeType($path);                                   // Obtiene el tipo MIME del archivo para indicar correctamente el tipo de contenido.
        
                return response($file, 200)->header("Content-Type", $mimeType);      // Retorna la imagen con un código de respuesta 200 y el tipo MIME correspondiente.
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'La imagen no existe.'
            ];

            return response()->json($data, $data['code']);
        }
    }
}
