<?php

namespace App\Services;


class CourseFormatterService
{


    public function format($content)
    {


        // Supprimer les éléments StudyRaid inutiles

        $remove = [

            'open navigation menu',
            'Créer un cours avec l\'IA',
            'Poser une question',
            'Créez votre premier cours',
            'Commencer',
            'Quiz',
            'Résumé',
            'Examen',
            'Cartes mémoire',
            'Jeu',
            'Générer avec l\'IA',
            'ProAudio',
            'Vidéo',
            'Illustration',
            'Certification'

        ];



        foreach($remove as $item)
        {
            $content = str_replace($item,'',$content);
        }



        // Nettoyage

        $content = preg_replace('/\s+/', ' ', $content);



        // Création des titres

        $replacements = [

            'Définition et Périmètre des Finances Publiques'
            =>
            "\n\n# Définition et Périmètre des Finances Publiques\n\n",


            'Les Ressources de l\'État'
            =>
            "\n\n# Les Ressources de l'État\n\n",


            'Les Dépenses de l\'État'
            =>
            "\n\n# Les Dépenses de l'État\n\n",


            'Les Principes Budgétaires Classiques et Modernes'
            =>
            "\n\n# Les Principes Budgétaires\n\n",


            'Le Contrôle des Finances Publiques'
            =>
            "\n\n# Le Contrôle des Finances Publiques\n\n",


        ];



        foreach($replacements as $old=>$new)
        {
            $content = str_replace($old,$new,$content);
        }



        return trim($content);


    }


}