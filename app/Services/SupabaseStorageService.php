<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class SupabaseStorageService
{
    public function upload(UploadedFile $file)
    {
        $filename = 'documents/' . uniqid() . '.' . $file->getClientOriginalExtension();

        $url = config('services.supabase.url');
        $key = config('services.supabase.key');


        $response = Http::withHeaders([

            'Authorization' => 'Bearer '.$key,

            'apikey' => $key,

            'Content-Type' => $file->getMimeType(),

        ])->withBody(

            file_get_contents($file->getRealPath()),

            $file->getMimeType()

        )->post(

            $url . '/storage/v1/object/task-documents/' . $filename

        );


        if ($response->failed()) {

            throw new \Exception(
                'Erreur Supabase : '.$response->body()
            );

        }


        return $url .
            '/storage/v1/object/public/task-documents/' .
            $filename;
    }
}