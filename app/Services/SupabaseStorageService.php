<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseStorageService
{
    public function upload($file)
    {
        $filename = 'documents/' . uniqid() . '.' . $file->getClientOriginalExtension();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.env('SUPABASE_KEY'),
            'apikey' => env('SUPABASE_KEY'),
            'Content-Type' => $file->getMimeType(),
        ])
        ->withBody(
            file_get_contents($file->getRealPath()),
            $file->getMimeType()
        )
        ->post(
            env('SUPABASE_URL')
            . '/storage/v1/object/'
            . 'task-documents/'
            . $filename
        );

        if ($response->failed()) {
            throw new \Exception(
                'Erreur Supabase : '.$response->body()
            );
        }


        return env('SUPABASE_URL')
            . '/storage/v1/object/public/task-documents/'
            . $filename;
    }
}