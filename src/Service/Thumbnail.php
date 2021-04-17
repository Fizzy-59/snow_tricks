<?php


namespace App\Service;


use App\Entity\Image;

class Thumbnail
{
    public function resize(Image $image)
    {
        // On va resize l'image en 16x9 généré plus tôt !!!
        $fullPath = $image->getPath() . '/cropped/' . $image->getName();
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $savingFullPath = $image->getPath() . '/thumbnail/' . $image->getName();
        $newWidth = 500;
        $newHeight = 281.25;

        // On utilise la bonne fonction de création d'image dans GD en fonction de l'extension
        if($extension === 'jpeg' || $extension === 'jpg')
        {
            $originalImg = imagecreatefromjpeg($fullPath);
        }
        else if($extension == 'png')
        {
            $originalImg = imagecreatefrompng($fullPath);
        }

        // Si le dossier thumbnail n'existe pas encore, on le crée, les fonctions imagejpeg et imagepng de GD ne le font pas !
        if (!file_exists($image->getPath() . '/thumbnail'))
        {
            mkdir($image->getPath() . '/thumbnail', 0777, true);
        }

        // Récupération de la taille de l'image source
        list($width, $height) = getimagesize($fullPath);

        // On resize si l'image n'est pas déjà en-dessous ou égal à la taille ciblé
        if(!(($width <= $newWidth) && ($height <= $newHeight)))
        {
            // Création du thumbnail
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            // Redimensionnement
            $resizeSuccess = imagecopyresized($thumbnail, $originalImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            if ($resizeSuccess !== FALSE)
            {
                // On utilise la bonne fonction de création d'image en fonction de l'extension
                if($extension === 'jpeg' || $extension === 'jpg')
                {
                    imagejpeg($thumbnail, $savingFullPath);
                }
                else if($extension == 'png')
                {
                    imagepng($thumbnail, $savingFullPath);
                }
                // On détruit l'image resizée chargée en RAM
                imagedestroy($thumbnail);
            }
        }
        else
        {
            // On utilise la bonne fonction de création d'image en fonction de l'extension
            if($extension === 'jpeg' || $extension === 'jpg')
            {
                imagejpeg($originalImg, $savingFullPath);
            }
            else if($extension == 'png')
            {
                imagepng($originalImg, $savingFullPath);
            }
        }
        // On détruit l'image original chargée en RAM
        imagedestroy($originalImg);
    }
}