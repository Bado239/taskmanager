<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorageService
{

    public function upload(UploadedFile $file)
    {

        $filename = 'documents/' . Str::uuid() . '.' . $file->getClientOriginalExtension();


        $response = Http::withHeaders([

            'Authorization' => 'Bearer ' . config('services.supabase.key'),

            'apikey' => config('services.supabase.key'),

            'Content-Type' => $file->getMimeType(),

        ])
        ->withBody(

            file_get_contents($file->getRealPath()),

            $file->getMimeType()

        )
        ->post(

            config('services.supabase.url')
            . '/storage/v1/object/task-documents/'
            . $filename

        );


        if ($response->failed()) {

            throw new \Exception(
                'Erreur Supabase : ' . $response->body()
            );

        }


        return config('services.supabase.url')
            . '/storage/v1/object/public/task-documents/'
            . $filename;

    }

}