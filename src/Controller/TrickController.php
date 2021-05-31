<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick/create", name="trick_create")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function newTrick(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setUser($user);
            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Trick added successfully');
            return $this->redirectToRoute('home');
        }

        return $this->render('trick/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/trick/{slug}", name="trick", requirements={"id":"\d+"})
     *
     * @param Trick $trick
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function showTrick(Trick $trick, CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();
        $comments = $commentRepository->loadComments(0, 10, $trick);
        $totalPages = $commentRepository->countTotalPages($trick);

        return $this->render('trick/index.html.twig',
            [
                'user' => $user,
                'trick' => $trick,
                'comments' => $comments,
                'totalPages' => $totalPages,
                'currentPage' => 1
            ]);
    }

    /**
     * @Route("/trick/{slug}/edit", name="trick_edit")
     *
     * @param Trick $trick
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editTrick(Trick $trick, EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(TrickType::class, $trick);
        $form->remove('mainImage');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/edit.html.twig', ['form' => $form->createView(), 'trick' => $trick]);
    }

    /**
     * @Route("/trick/{id}/delete", name="trick_delete")
     *
     * @param Trick $trick
     * @param EntityManagerInterface $entityManager
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function deleteTrick(Trick $trick, EntityManagerInterface $entityManager, TrickRepository $trickRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager->remove($trick);
        $entityManager->flush();

        $tricks = $trickRepository->findAll();
        return $this->render('home_page/index.html.twig', ['tricks' => $tricks]);
    }
}

