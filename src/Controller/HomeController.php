<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/profile", name="profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profileAction(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
         $password = $passwordEncoder->encodePassword($user, $user->getPassword());

                   $user->setPassword($password);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_logout');

        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' =>$form->createView(),
        ]);
    }

    public function example(SessionInterface $session)
    {
        // donne un nom à la variable de session et une valeur
        $session->set('toto', 3);

        // récupère la valeur de la variable de session
        $getToto = $session->get('toto');
        // $getToto = 3
    }


}
