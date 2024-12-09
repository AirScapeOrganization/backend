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

    public function uploadImage($file)
    {
        try {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $fileContent = file_get_contents($file->getRealPath());
                $fileName = uniqid() . '-' . $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
            } else {
                throw new \Exception('Formato de archivo no soportado.');
            }

            Log::info('Archivo a subir:', [
                'filename' => $fileName,
                'mime_type' => $mimeType,
                'bucket' => $this->bucket,
            ]);


            $response = $this->client->request('POST', "object/{$this->bucket}/{$fileName}", [
                'headers' => [
                    'Content-Type' => $mimeType ?? 'application/octet-stream',
                ],
                'body' => $fileContent,
            ]);


            Log::info('Respuesta de Supabase:', [
                'status_code' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents(),
            ]);


            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $uploadedUrl = env('SUPABASE_URL') . "/storage/v1/object/public/{$this->bucket}/{$fileName}";
                return $uploadedUrl;
            } else {
                Log::error('Error al subir a Supabase: ' . $response->getBody()->getContents());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error al subir a Supabase: ' . $e->getMessage());
            return null;
        }
    }
}
