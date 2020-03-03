<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



class UserController extends AbstractController
{
    private $userRepository;
    private $eventDispatcher;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/user_list", name="user_list")
     */
    public function index()
    {
        $userList = $this->userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'user_list' => $userList,
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function log()
    {
        $userList = $this->userRepository->findAll();
        return $this->render('security/login.html.twig');
    }
    /**
     * @Route("/user-create", name="user-create")
     */
    public function newAction(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // objet User rempli avec les infos du formulaire
            //$userData = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            // pas besoin de cette ligne si on injecte ENtityManagerInterface en dépendance
//            $entityManager = $this->getDoctrine()->getManager();
            // dire à Doctrine que cet objet est nouveau
            $user->setCreationDate(new \DateTime('now'));
            $entityManager->persist($user);
            // enregistrer les nouveaux objets et object modifié en base de donnée
            // $this->eventDispatcher->dispatch(new UserRegisteredEvent ($user));
            // dump($this->eventDispatcher);
            $entityManager->flush();
            $this->addFlash('success', "L'utilisateur a bien été crée");

            return $this->redirectToRoute('login');

        }
        return $this->render('user/new.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete-bis/{id}", name="user_delete_bis")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteBis(string $id, EntityManagerInterface $entityManager)
    {
        $user = $this->userRepository->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('notice', "L'utilisateur a bien été supprimé");

        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("/deleteUser/{id}", name="user_delete")
     * @ParamConverter("user", options={"mapping"={"id"="id"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(user $user, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('notice', "L'utilisateur a bien été supprimé");

        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("/editUser/{id}", name="user_edit")
     * @ParamConverter("user", options={"mapping"={"id"="id"}})
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Request $request, user $user, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(userType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('notice', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
