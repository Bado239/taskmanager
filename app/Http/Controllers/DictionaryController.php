<?php

namespace App\Http\Controllers;

use App\Models\DictionaryWord;


class DictionaryController extends Controller
{


public function search($word)
{


$word = strtolower(trim($word));


$result = DictionaryWord::where(
'word',
$word
)->first();



if($result)
{


return response()->json([

"word"=>$word,

"definition"=>$result->definition

]);


}



return response()->json([

"word"=>$word,

"definition"=>"Mot non encore enregistré dans votre dictionnaire."

]);


}



}