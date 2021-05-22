<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\TrickType;
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
     * @Route("/trick/{id}", name="trick", requirements={"id":"\d+"})
     *
     * @param Trick $trick
     * @return Response
     */
    public function showTrick(Trick $trick): Response
    {
        $user = $this->getUser();
        return $this->render('trick/index.html.twig', ['user' => $user, 'trick' => $trick]);
    }

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

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/add-comment", name="add_comment")
     *
     * @param TrickRepository $trickRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function addComment(TrickRepository $trickRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
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

        return $this->redirect($this->generateUrl('trick', ['id' => $trick->getId()]));
    }

    /**
     * @Route("/trick/{id}/edit", name="trick_edit")
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

