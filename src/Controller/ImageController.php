<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/image/delete", name="image_delete")
     */
    public function delete(): Response
    {
        // Delete image in tricks
        // Delete image in folder cropped
        // Delete image in thumbnail cropped
        // Return to edit form

        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }
}
