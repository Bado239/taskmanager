<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

class DocumentReaderService
{

    public function read($document)
    {

        // Fichier PDF ou Word dans Supabase
        if($document->file_path)
        {

            $url = "https://zhlojjivmwuuqhzeqpgg.supabase.co/storage/v1/object/public/ebooks/"
                .$document->file_path;

            $content = Http::get($url)->body();


            $extension = strtolower(
                pathinfo($document->file_path, PATHINFO_EXTENSION)
            );


            if($extension === 'pdf')
            {
                return $this->readPdf($content);
            }


            if(in_array($extension,['doc','docx']))
            {
                return $this->readWord($content);
            }

        }



        // Lien Internet
        if($document->url)
        {

            return $this->readUrl(
                $document->url
            );

        }


        return '';

    }




    private function readPdf($content)
    {

        $temp = tempnam(
            sys_get_temp_dir(),
            'pdf'
        );


        file_put_contents(
            $temp,
            $content
        );


        $parser = new Parser();


        $pdf = $parser->parseFile($temp);


        unlink($temp);


        return $pdf->getText();

    }





    private function readWord($content)
    {

        $temp = tempnam(
            sys_get_temp_dir(),
            'docx'
        );


        file_put_contents(
            $temp,
            $content
        );


        $zip = new \ZipArchive();


        if($zip->open($temp) === true)
        {


            $xml = $zip->getFromName(
                'word/document.xml'
            );


            $zip->close();


            unlink($temp);


            if($xml)
            {

                // Supprimer les balises XML
                $xml = str_replace(
                    ['</w:p>', '</w:tab>'],
                    ["\n", " "],
                    $xml
                );


                $text = strip_tags($xml);


                // Nettoyage espaces
                $text = html_entity_decode(
                    $text
                );


                return trim(
                    preg_replace(
                        '/\s+/',
                        ' ',
                        $text
                    )
                );

            }

        }


        unlink($temp);


        return '';

    }


    private function readUrl($url)
    {

        try {

            $response = Http::timeout(20)
                ->get($url);


            return strip_tags(
                $response->body()
            );


        } catch(\Exception $e){

            return '';

        }

    }


}