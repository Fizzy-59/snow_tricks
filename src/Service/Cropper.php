<?php


namespace App\Service;


use App\Entity\Image;

class Cropper
{
    public function crop(Image $image)
    {
        $fullPath = $image->getPath() . '/' . $image->getName();
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $savingFullPath = $image->getPath() . '/cropped/' . $image->getName();

        // On utilise la bonne fonction de création d'image dans GD en fonction de l'extension
        if($extension === 'jpeg' || $extension === 'jpg')
        {
            $originalImg = imagecreatefromjpeg($fullPath);
        }
        else if($extension == 'png')
        {
            $originalImg = imagecreatefrompng($fullPath);
        }

        $ratio16x9 = 16 / 9;
        $ratio = imagesx($originalImg) / imagesy($originalImg);

        // Si le dossier cropped n'existe pas encore, on le crée, les fonctions imagejpeg et imagepng de GD ne le font pas !
        if (!file_exists($image->getPath() . '/cropped'))
        {
            mkdir($image->getPath() . '/cropped', 0777, true);
        }

        // On crop si l'image elle n'est pas en 16x9, si elle l'est déjà, on la copie juste dans le dossier 'cropped'
        if(!(round($ratio, 2) === round($ratio16x9, 2)))
        {
            if($ratio < $ratio16x9)
            {
                $width = imagesx($originalImg);
                $height = (imagesx($originalImg) / 16) * 9;
            }
            else if($ratio > $ratio16x9)
            {
                $width = (imagesy($originalImg) / 9) * 16;
                $height = imagesy($originalImg);
            }

            $croppedImg = imagecrop($originalImg, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $height]);

            if ($croppedImg !== FALSE)
            {
                // On utilise la bonne fonction de création d'image en fonction de l'extension
                if($extension === 'jpeg' || $extension === 'jpg')
                {
                    imagejpeg($croppedImg, $savingFullPath);
                }
                else if($extension == 'png')
                {
                    imagepng($croppedImg, $savingFullPath);
                }
                // On détruit l'image rognée chargée en RAM
                imagedestroy($croppedImg);
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