<?php


namespace App\Outils;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class chargerPhoto
{

    public function enregistrer($name, String $directory, UploadedFile $uploadedFile) {

        $fileName = "nomParDefaut.png";

        if ($uploadedFile) {
            //création d'un nom pour mon fichier
            $fileName = $name.'-'.uniqid().'.'.$uploadedFile->guessExtension();
            //sauvegarde de mon fichier dans le répertoire de mon choix (cf config.services.yaml)
            $uploadedFile->move($directory, $fileName);
        }
        return $fileName;
    }
}