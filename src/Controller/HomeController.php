<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for handling the home page.
 */
class HomeController extends AbstractController
{
    /**
     * Renders the home page.
     *
     * @return Response The HTTP response.
     */
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }
}
