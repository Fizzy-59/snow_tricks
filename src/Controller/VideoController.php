<?php

namespace App\Controller;

use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    /**
     * @Route("/edit/video", name="video_edit")
     *
     * @param VideoRepository $videoRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(VideoRepository $videoRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $videoId = $request->query->get('id');
        $video = $videoRepository->findOneBy(['id' => $videoId]);

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $url = $form->get('url')->getData();
            $video->setUrl($url);
            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('video/edit.html.twig', ['form' => $form->createView(), 'video' => $video]);
    }

    /**
     * @Route("/delete/video", name="video_delete")
     */
    public function delete(VideoRepository $videoRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $videoId = $request->query->get('id');
        $video = $videoRepository->findOneBy(['id' => $videoId]);
        $entityManager->remove($video);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
