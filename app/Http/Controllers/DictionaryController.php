<?php

namespace App\Http\Controllers;


class DictionaryController extends Controller
{

public function search($word)
{


$data=[


"moissonneur" =>
"Personne qui récolte les céréales.",


"prestesse" =>
"Rapidité et habileté dans l'action.",


"faucille" =>
"Outil utilisé pour couper les plantes."


];


return response()->json([

"word"=>$word,

"definition" =>
$data[strtolower($word)]
?? 
"Aucune définition trouvée."

]);


}

}