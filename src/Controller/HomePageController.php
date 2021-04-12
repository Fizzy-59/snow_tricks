<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     *
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();

        return $this->render('home_page/index.html.twig',
        [
            'controller_name' => 'HomePageController',
            'tricks' => $tricks,
        ]);
    }
}
