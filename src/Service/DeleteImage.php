<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class DeleteImage
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // TODO : migrate all service in one service: IMAGE
    public function deleteImage($image)
    {
        $fileSystem = new Filesystem();

        // Delete image in folder tricks
        $fileSystem->remove($image->getPath() . '/' . $image->getName());
        // Delete image in folder cropped
        $fileSystem->remove($image->getPath() . '/cropped/' . $image->getName());
        // Delete image in folder thumbnail
        $fileSystem->remove($image->getPath() . '/thumbnail/' . $image->getName());
        //Delete object image
        $this->entityManager->remove($image);
        $this->entityManager->flush();
    }
}