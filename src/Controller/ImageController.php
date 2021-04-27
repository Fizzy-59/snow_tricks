<?php

namespace App\Controller;

use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\BadMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/image/edit", name="image_edit")
     */
    public function edit(Request $request, ImageRepository $imageRepository, EntityManagerInterface $entityManager,
                         ImageManager $imageManager): Response
    {
        $imageId = $request->query->get('id');
        $image = $imageRepository->findOneBy(['id' => $imageId]);

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $image = $form->getData();

            $imageFile = $imageManager->saveImage($image);

            $imageManager->crop($imageFile);
            $imageManager->resize($imageFile);

            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('image/edit.html.twig', ['form' => $form->createView(), 'image' => $image]);
    }

    /**
     * @Route("/main-image/edit", name="main_image_edit")
     */
    public function editMainImage(Request $request, ImageRepository $imageRepository, EntityManagerInterface $entityManager,
                                  ImageManager $imageManager): Response
    {
        $imageId = $request->query->get('id');
        $image = $imageRepository->findOneBy(['id' => $imageId]);

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $image = $form->getData();

            $imageFile = $imageManager->saveImage($image);

            $imageManager->crop($imageFile);
            $imageManager->resize($imageFile);

            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('image/main_image_edit.html.twig', ['form' => $form->createView(), 'image' => $image]);
    }

    /**
     * @Route("/main-image/delete/id", name="main_image_delete")
     */
    public function deleteMainImage(ImageRepository $imageRepository, Request $request, ImageManager $imageManager,
        EntityManagerInterface $entityManager): Response
    {
        $imageId = $request->query->get('id');
        $image = $imageRepository->findOneBy(['id' => $imageId]);
        $trick = $image->getTrick();
        $collectionOfImages = $trick->getImages();
        // TODO : use object
        $nbOfImages = count($collectionOfImages);

        // TODO: personalize message and redirect
        // If there is one image or less, it is impossible to delete the main image because it cannot be replaced.
        if($nbOfImages <= 1) throw new BadMessageException('joe');

        $imageManager->deleteImage($image);
        // We delete the main image, we must assign a new main image
        $trick->setMainImage($trick->getImages()->first());

        $entityManager->persist($trick);
        $entityManager->flush();

        $url = $request->headers->get('referer');
        return $this->redirect($url);
    }

    /**
     * @Route("/image/delete/id", name="image_delete")
     */
    public function deleteImage(ImageRepository $imageRepository, Request $request, ImageManager $imageManager): Response
    {
        $trickId = $request->query->get('id');
        $image = $imageRepository->findOneBy(['id' => $trickId]);
        $imageManager->deleteImage($image);

        $url = $request->headers->get('referer');
        return $this->redirect($url);
    }
}
