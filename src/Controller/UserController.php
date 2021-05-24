<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/gravatar", name="gravatar")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/gravatar/add", name="add_gravatar")
     *
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function add(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $gravatarUrl = 'http://www.gravatar.com/avatar/' . md5($user->getUsername()) . '?s=32';
        $user->setGravatar($gravatarUrl);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->render('home_page/index.html.twig');
    }
}
