<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use App\Models\Listings;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class ListingsController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {

        $listings = Listings::with('mainPhoto')->get();

        if ($listings->isEmpty()) {
            return response()->json(['message' => 'No listings found'], 404);
        }

        $formattedListings = $listings->map(function ($listing) {
            return [
                'id' => $listing->listing_id,
                'title' => $listing->title,
                'description' => $listing->description,
                'address' => $listing->address,
                'latitude' => $listing->latitude,
                'longitude' => $listing->longitude,
                'price_per_night' => $listing->price_per_night,
                'num_bedrooms' => $listing->num_bedrooms,
                'num_bathrooms' => $listing->num_bathrooms,
                'max_guests' => $listing->max_guests,
                'user_id' => $listing->user_id,
                'photo_url' => optional($listing->mainPhoto)->photo_url,
            ];
        });

        return response()->json(['properties' => $formattedListings, 'status' => 200]);
    }


        public function store(Request $request)
        {
            $user = $request->user();

            if ($user->is_owner !== 1) {
                return response()->json([
                    'message' => 'No tienes permisos para crear una propiedad'
                ], 403);
            }

            $validated = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'required|string',
                'address' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'price_per_night' => 'required|numeric',
                'num_bedrooms' => 'required|integer',
                'num_bathrooms' => 'required|integer',
                'max_guests' => 'required|integer',
                'photos' => 'required|array',
                'photos.*' => 'file|mimes:jpg,jpeg,png',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de datos',
                    'errors' => $validated->errors(),
                    'status' => 400
                ], 400);
            }

            DB::beginTransaction();

            try {

                $listing = Listings::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'address' => $request->address,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'price_per_night' => $request->price_per_night,
                    'num_bedrooms' => $request->num_bedrooms,
                    'num_bathrooms' => $request->num_bathrooms,
                    'max_guests' => $request->max_guests,
                    'user_id' => $user->user_id,
                ]);

                if (!$listing->listing_id) {
                    throw new \Exception("No se pudo obtener el ID de la propiedad.");
                }

                $uploadedUrls = [];

                foreach ($request->file('photos') as $photo) {
                    $uploadedUrl = $this->supabase->uploadImage($photo);

                    if (!$uploadedUrl) {
                        throw new \Exception("Error al subir una de las imágenes.");
                    }

                    DB::table('photos')->insert([
                        'listing_id' => $listing->listing_id,
                        'photo_url' => $uploadedUrl,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $uploadedUrls[] = $uploadedUrl;
                }

                DB::commit();

                return response()->json([
                    'message' => 'Propiedad creada exitosamente',
                    'listing' => $listing,
                    'photos' => $uploadedUrls,
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'message' => 'Error al crear la propiedad',
                    'error' => $e->getMessage(),
                    'status' => 500
                ], 500);
            }
        }


    public function show($id)
    {
        $listing = Listings::with('photos')->find($id);

        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        return response()->json([
            'listing' => [
                'id' => $listing->listing_id,
                'title' => $listing->title,
                'description' => $listing->description,
                'address' => $listing->address,
                'latitude' => $listing->latitude,
                'longitude' => $listing->longitude,
                'price_per_night' => $listing->price_per_night,
                'num_bedrooms' => $listing->num_bedrooms,
                'num_bathrooms' => $listing->num_bathrooms,
                'max_guests' => $listing->max_guests,
                'user_id' => $listing->user_id,
            ],
            'photos' => $listing->photos,
        ], 200);
    }




    public function edit(Request $request, string $id)
    {
        $listings = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price_per_night' => 'required|numeric',
            'num_bedrooms' => 'required|integer',
            'num_bathrooms' => 'required|integer',
            'max_guests' => 'required|integer',
        ]);

        if ($listings->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos',
                'error' => $listings->errors(),
                'status' => 400
            ]);
        }

        $listing = Listings::find($id);

        if (!$listing) {
            return response()->json([
                'mensaje' => 'Propiedad no encontrada',
                'status' => 404
            ]);
        }

        $listing->title = $request->title;
        $listing->description = $request->description;
        $listing->address = $request->address;
        $listing->latitude = $request->latitude;
        $listing->longitude = $request->longitude;
        $listing->price_per_night = $request->price_per_night;
        $listing->num_bedrooms = $request->num_bedrooms;
        $listing->num_bathrooms = $request->num_bathrooms;
        $listing->max_guests = $request->max_guests;

        if (!$listing->save()) {
            return response()->json([
                'mensaje' => 'No se pudo actualizar la propiedad',
                'status' => 500
            ]);
        }

        return response()->json([
            'mensaje' => 'Propiedad actualizada correctamente',
            'status' => 200
        ]);
    }


    public function update(Request $request, string $id)
    {
        // Implementar lógica para actualización de listado
    }

    public function destroy(string $id)
    {
        // Implementar lógica para eliminar listado
    }
}
