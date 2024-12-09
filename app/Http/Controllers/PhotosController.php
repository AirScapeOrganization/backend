<?php 

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhotosController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabase = $supabaseService;
    }

    public function store(Request $request)
    {
        // Validación de la imagen
        $validated = Validator::make($request->all(), [
            'photo_url' => 'required|file|mimes:jpg,jpeg,png',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $validated->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $uploadedUrl = $this->supabase->uploadImage($request->file('photo_url'));

            if (!$uploadedUrl) {
                return response()->json([
                    'mensaje' => 'Error al subir la imagen a Supabase',
                    'status' => 500,
                    'url' => $uploadedUrl,
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error durante la subida de la imagen: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }

        // Guardar la URL en la base de datos
        try {
            $photo = new Photo();
            $photo->photo_url = $uploadedUrl; // Guardar la URL generada
            $photo->save();

            return response()->json([
                'mensaje' => 'Imagen subida y URL guardada exitosamente',
                'photo' => $photo,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al guardar la URL en la base de datos',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
