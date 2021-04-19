<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/image/delete", name="image_delete")
     */
    public function delete(TrickRepository $trickRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $imageId = $request->query->get('id');
        $image = $trickRepository->findOneBy(['id' => $imageId]);


        $fileSystem = new Filesystem();

        // Delete image in folder tricks
        $fileSystem->remove($image->getPath() . '/' . $image->getName());

        // Delete image in folder cropped
        $fileSystem->remove($image->getPath() . '/cropped/' . $image->getName());

        // Delete image in folder thumbnail
        $fileSystem->remove($image->getPath() . '/thumbnail/' . $image->getName());

        $entityManager->persist($image);
        $entityManager->flush();

        $this->addflash('success', "Successfully deleted image");

        // Return to edit form

        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }
}
