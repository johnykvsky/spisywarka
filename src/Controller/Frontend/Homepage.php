<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Homepage extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     * @return Response
     */
    public function homepage(): Response
    {
        return $this->render('frontend/homepage.html.twig');
    }
}
