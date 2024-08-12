<?php

// src/Controller/SecurityController.php

namespace App\Controller;

use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller for handling security-related actions.
 */
class SecurityController extends AbstractController
{
    /**
     * Handles user login.
     *
     * @param AuthenticationUtils $authenticationUtils The authentication utils.
     *
     * @return Response The HTTP response.
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        /** @var User $user */
        $user = $this->getUser();
        if ($user && !$user->isVerified()) {
            $this->addFlash('warning', 'Your account requires verification. Please check your email.');
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Handles user logout.
     *
     * @throws Exception
     */
    public function logout(): void
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
