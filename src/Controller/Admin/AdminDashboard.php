<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboard extends AbstractController
{
    /**
     * @Route("/dashboard", name="admin_dashboard", methods={"GET"})
     * @return Response
     */
    public function createOrEditItem(): Response
    {
        return $this->render('dashboard.html.twig');
    }
}
