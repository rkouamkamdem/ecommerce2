<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BonjourController extends AbstractController
{
    /**
     * @Route("/bonjour", name="app_bonjour")
     */
    public function index(): Response
    {
        return $this->render('bonjour/password.html.twig', [
            'controller_name' => 'BonjourController',
        ]);

    }
}
