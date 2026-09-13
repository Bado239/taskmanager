<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;


class BookTextReaderController extends Controller
{


    public function show($id)
    {


        $book = DB::table('personal_resources')
            ->where('id',$id)
            ->first();



        if(!$book)
        {
            abort(404);
        }



        $parser = new Parser();


        $pdf = $parser->parseFile(
            $book->pdf_path
        );



        $text = $pdf->getText();



        return view(
            'book.book-reader-text',
            [
                'book'=>$book,
                'text'=>$text
            ]
        );


    }


}