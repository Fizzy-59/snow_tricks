<?php

namespace App\Controller;

use App\Entity\Image;
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
     * @Route("/image/{id}/edit", name="image_edit")
     *
     * @param Image $image
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editImage(Image $image, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->getData();
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('image/edit.html.twig', ['form' => $form->createView(), 'image' => $image]);
    }

    /**
     * @Route("/main-image/{id}/edit", name="main_image_edit")
     *
     * @param Image $image
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editMainImage(Image $image, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->getData();
            // TODO: set new MainImage()

            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('image/main_image_edit.html.twig', ['form' => $form->createView(), 'image' => $image]);
    }

    /**
     * @Route("/main-image/{id}/delete", name="main_image_delete")
     *
     * @param Image $image
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteMainImage(Image $image, Request $request, EntityManagerInterface $entityManager): Response
    {
        $trick = $image->getTrick();
        $collectionOfImages = $trick->getImages();
        // TODO : use object
        $nbOfImages = count($collectionOfImages);

        // TODO: personalize message and redirect
        // If there is one image or less, it is impossible to delete the main image because it cannot be replaced.
        if ($nbOfImages <= 1) throw new BadMessageException('joe');

        $imageManager->deleteImage($image);
        // We delete the main image, we must assign a new main image
        $trick->setMainImage($trick->getImages()->first());

        $entityManager->persist($trick);
        $entityManager->flush();

        $url = $request->headers->get('referer');
        return $this->redirect($url);
    }

    /**
     * @Route("/image/delete", name="image_delete")
     */
    public function deleteImage(ImageRepository $imageRepository, Request $request, ImageManager $imageManager): Response
    {
        $trickId = $request->query->get('id');
        $image = $imageRepository->findOneBy(['id' => $trickId]);
        $imageManager->deleteImage($image);

        $url = $request->headers->get('referer');
        return $this->redirect($url);
    }

    /**
     * @Route("/image/add", name="image_add")
     */
    public function addImage(EntityManagerInterface $entityManager, ImageManager $imageManager, Request $request): Response
    {
        // TODO: need to fix add mutiples images
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($trick->getImages() as $image) {
                $image->setTrick($trick);
                $image->setCaption($image->getCaption());
                $image = $imageManager->saveImage($image);

                $entityManager->persist($image);

                $imageManager->crop($image);
                $imageManager->resize($image);
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('image/add_image.html.twig', ['form' => $form->createView()]);

    }
}
