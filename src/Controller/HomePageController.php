<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     *
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function showHome(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();
        return $this->render('home_page/index.html.twig', ['tricks' => $tricks]);
    }
}
