<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\post;
use App\Form\CommentType;
use App\Form\postType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class postController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @Route("/post", name="post_")
 */
class PostController extends AbstractController
{
    private $manager;
    private $PostRepository;

    public function __construct(EntityManagerInterface $manager, PostRepository $PostRepository)
    {
        $this->manager = $manager;
        $this->PostRepository = $PostRepository;
    }

    /**
     * @Route("/", name="list")
     */
    public function list()
    {
        $posts = $this->PostRepository->findBy(['isPublish' => true]);

        return $this->render('Post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/my-list", name="my_list")
     */
    public function myList()
    {
        $posts = $this->PostRepository->findBy(['author' => $this->getUser()]);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/{id}", name="infos")
     * @ParamConverter("post", options={"id"="id"})
     */
    public function infos(Request $request, post $post)
    {
        $comment = new Comments();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setpost($post);

            $this->manager->persist($comment);
            $this->manager->flush();

            $this->addFlash('success', 'Comment successfully posted.');
        }

        return $this->render('post/info.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/add", name="add")
     * @Route("/edit/{id}", name="edit")
     * @ParamConverter("post", options={"id"="id"})
     */
    public function form(Request $request, post $post = null)
    {
        if ($post === null) {
            $post = new Post();
            $post->setAuthor($this->getUser());
        }

        $form = $this->createForm(postType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($post);
            $this->manager->flush();

            $this->addFlash('success', 'Your post has been send successfully.');
            return $this->redirectToRoute('post_list');
        }

        return $this->render('post/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/approve/{id}", name="approve")
     * @ParamConverter("post", options={"id"="id"})
     */
    public function approve(post $post = null)
    {
        if($post->getApprover()->contains($this->getUser())){
            $post->removeApprover($this->getUser());
            $this->addFlash('success', 'Approval deleted.');
        }else{
            $post->addApprover($this->getUser());
            $this->addFlash('success', 'Approval added.');
        }

        $this->manager->persist($post);
        $this->manager->flush();

        return $this->redirectToRoute('post_infos', ['id' => $post->getId()]);
    }
}