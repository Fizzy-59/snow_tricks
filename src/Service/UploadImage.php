<?php


namespace App\Service;


use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadImage extends AbstractController
{

    public function saveImage(Image $image): Image
    {
        // Récupère le fichier de l'image uploadée
        $file = $image->getFile();
        // Créer un nom unique pour le fichier
        $name = md5(uniqid()) . '.' . $file->guessExtension();
        // Déplace le fichier
        $path = 'img/tricks';
        $file->move($path, $name);

        // Donner le path et le nom au fichier dans la base de données
        $image->setPath($path);
        $image->setName($name);

        return $image;
    }
}