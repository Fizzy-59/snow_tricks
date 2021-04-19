<?php


namespace App\Service;


use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadImage extends AbstractController
{

    /**
     * Upload an image
     *
     * @param Image $image
     * @return Image
     */
    public function saveImage(Image $image): Image
    {
        $file = $image->getFile();
        $name = md5(uniqid()) . '.' . $file->guessExtension();
        $path = 'img/tricks';
        $file->move($path, $name);

        $image->setPath($path);
        $image->setName($name);

        return $image;
    }
}