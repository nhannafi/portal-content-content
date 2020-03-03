<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AuthentificatorController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // si l'utilisateur accède à "\login" en étant déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('user_list');
        }

        // récupérer l'erreur de connexion si il y a
        $error = $authenticationUtils->getLastAuthenticationError();
        // dernier email entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/login-success", name="login-success")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function connectSuccess(SessionInterface $session)
    {
        // donne un nom à la variable de session et une valeur
        $session->set('toto', 3);

        // récupère la valeur de la variable de session
        $getToto = $session->get('toto');
        // $getToto = 3

        return $this->render('security/success.html.twig');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
