<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick", name="trick")
     *
     * @param TrickRepository $trickRepository
     * @param Request $request
     *
     * @return Response
     */
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        $trickId = $request->query->get('id');
        $trick = $trickRepository->findOneBy(['id' => $trickId]);

        return $this->render('trick/index.html.twig', ['controller_name' => 'TrickController', 'trick' => $trick]);
    }

    /**
     * @Route("/add-comment", name="add_comment")
     *
     * @param TrickRepository $trickRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     *
     * @return Response
     */
    public function addComment(TrickRepository $trickRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        $request = $request->request->all();
        $trickId = $request['trickId'];
        $content = $request['comment'];

        $trick = $trickRepository->findOneBy(['id' => $trickId]);

        $comment = new Comment();
        $trick->addComment($comment);
        $comment->setContent($content);
        $comment->setUser($user);
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->render('trick/index.html.twig', ['controller_name' => 'TrickController', 'trick' => $trick]);
    }

    /**
     * @Route("/trick/create", name="trick_create")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse|Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager, ImageManager $imageManager): Response
    {
        $user = $this->getUser();

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $trick->setName($form->get('name')->getData());
            $trick->setDescription($form->get('description')->getData());
            $trick->setUser($user);

            $mainImage = $trick->getMainImage();
            $mainImage->setTrick($trick);
            $mainImage = $imageManager->saveImage($mainImage);

            $entityManager->persist($mainImage);

            $imageManager->crop($mainImage);
            $imageManager->resize($mainImage);

            foreach ($trick->getImages() as $image) {
                $image->setTrick($trick);
                $image->setCaption($image->getCaption());
                $image = $imageManager->saveImage($image);

                $entityManager->persist($image);

                $imageManager->crop($image);
                $imageManager->resize($image);
            }

            echo '<pre>';
            var_dump($trick->getVideos());
            die();
            foreach ($trick->getVideos() as $video) {
                echo '<pre>';
                var_dump($video->getUrl());
                die();

                $video->setTrick($trick);
                $video->setUrl($video->getUrl());
                $entityManager->persist($video);
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/trick/edit", name="trick_edit")
     *
     */
    public function edit(Request $request, TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $trickId = $request->query->get('id');
        $trick = $trickRepository->findOneBy(['id' => $trickId]);

        $form = $this->createForm(TrickType::class, $trick);
        $form->remove('images');
        $form->remove('mainImage');
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $name = $form->get('name')->getData();
            $description = $form->get('description')->getData();

            $trick->setName($name);
            $trick->setDescription($description);

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/edit.html.twig', ['form' => $form->createView(), 'trick' => $trick]);
    }
}
