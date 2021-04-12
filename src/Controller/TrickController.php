<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CreateTrickType;


use App\Repository\TrickRepository;
use App\Service\UploadImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

        return $this->render('figure/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick
        ]);
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

        return $this->render('figure/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/trick/create", name="trick_create")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UploadImage $uploadImage
     *
     * @return RedirectResponse|Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UploadImage $uploadImage)
    {
        $user = $this->getUser();

        $trick = new Trick();
        $form = $this->createForm(CreateTrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $trick->setName($form->get('name')->getData());
            $trick->setDescription($form->get('description')->getData());
            $trick->setUser($user);

            foreach ($trick->getImages() as $image) {
                $image->setTrick($trick);
                $image->setCaption($image->getCaption());
                $image = $uploadImage->saveImage($image);

                $entityManager->persist($image);
            }

            foreach ($trick->getVideos() as $video) {
                $video->setUrl($video->getUrl());

                $entityManager->persist($video);
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
