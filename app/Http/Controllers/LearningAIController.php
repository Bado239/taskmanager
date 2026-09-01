<?php

namespace App\Http\Controllers;


use App\Models\Task;
use App\Models\GeneratedCourse;
use Illuminate\Http\Request;


class LearningAIController extends Controller
{


public function generate(Task $task)
{


$documents = $task->learningDocuments;



$source = "";


foreach($documents as $document)
{

$source .= "\n";
$source .= $document->title;


if($document->url)
{
$source .= "\nLien : ".$document->url;
}

}



$prompt = "

Tu es un professeur universitaire.

Crée un cours complet niveau Master à partir des ressources suivantes :

".$source."


Structure obligatoirement :

- Introduction
- Objectifs
- Définitions
- Développement détaillé
- Exemples
- Points clés
- Questions de révision
- Résumé final


";



// TEMPORAIRE
// ici nous brancherons l'API IA


$content = "

# ".$task->title."


## Introduction

Cours généré automatiquement à partir des documents.


## Objectifs

Comprendre les notions principales.


## Développement

Analyse complète du chapitre.


## Résumé

Les éléments essentiels à retenir.

";



GeneratedCourse::create([

'task_id'=>$task->id,

'title'=>$task->title,

'content'=>$content

]);



return back()->with(
'success',
'Cours généré avec succès'
);


}


}