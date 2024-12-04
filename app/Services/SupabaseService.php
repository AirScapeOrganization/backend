<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
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
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->bucket = env('SUPABASE_BUCKET', 'default-bucket');
    }

    public function uploadImage($file, $customFileName = null)
    {
        try {
            if (is_string($file)) {
                $fileContent = Storage::get($file);
                $fileName = $customFileName ?? basename($file);
            } else {
                $fileContent = file_get_contents($file->getPathname());
                $fileName = $customFileName ?? $file->getClientOriginalName();
            }

            $uniqueFileName = uniqid() . '-' . $fileName;

            $response = $this->client->request('POST', "object/{$this->bucket}/$uniqueFileName", [
                'headers' => [
                    'Content-Type' => $file->getMimeType() ?? 'application/octet-stream',
                ],
                'body' => $fileContent,
            ]);

            if ($response->getStatusCode() === 200) {
                return env('SUPABASE_URL') . '/storage/v1/object/public/' . $this->bucket . '/' . $uniqueFileName;
            }
        } catch (\Exception $e) {
            Log::error('Error uploading to Supabase: ' . $e->getMessage());
            return null;
        }

        return null;
    }
}
