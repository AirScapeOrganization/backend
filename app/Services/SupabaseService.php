<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $client;
    protected $bucket;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('SUPABASE_URL') . '/storage/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
            ],
        ]);

        $this->bucket = env('SUPABASE_BUCKET', 'photos');
    }

    public function uploadImage($file, $user_id)
    {
        try {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $fileContent = file_get_contents($file->getRealPath());
                $fileName = uniqid() . '-' . $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
            } else {
                throw new \Exception('File format not supported.');
            }

            $filePath = "folder_user_{$user_id}/{$fileName}";

            $response = $this->client->request('POST', "object/{$this->bucket}/{$filePath}", [
                'headers' => [
                    'Content-Type' => $mimeType ?? 'application/octet-stream',
                ],
                'body' => $fileContent,
            ]);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $uploadedUrl = env('SUPABASE_URL') . "/storage/v1/object/public/{$this->bucket}/{$filePath}";
                return $uploadedUrl;
            } else {
                Log::error('Error uploading to Supabase:' . $response->getBody()->getContents());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error uploading to Supabase:' . $e->getMessage());
            return null;
        }
    }

    public function uploadProfilePicture($file, $user_id)
    {
        try {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $fileContent = file_get_contents($file->getRealPath());
                $fileName = 'profile_picture-' . uniqid() . '-' . $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
            } else {
                throw new \Exception('File format not supported.');
            }

            // Guardar la foto de perfil en la ruta específica
            $filePath = "folder_user_{$user_id}/profile_picture/{$fileName}";

            $response = $this->client->request('POST', "object/{$this->bucket}/{$filePath}", [
                'headers' => [
                    'Content-Type' => $mimeType ?? 'application/octet-stream',
                ],
                'body' => $fileContent,
            ]);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $uploadedUrl = env('SUPABASE_URL') . "/storage/v1/object/public/{$this->bucket}/{$filePath}";
                return $uploadedUrl;
            } else {
                Log::error('Error when uploading profile photo to Supabase:' . $response->getBody()->getContents());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error when uploading profile photo to Supabase:' . $e->getMessage());
            return null;
        }
    }
}
