<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SocialMediaController extends AbstractController
{
    /**
     * @Route("/social/media", name="social_media")
     */
    public function index()
    {
        return $this->render('social_media/index.html.twig', [
            'controller_name' => 'SocialMediaController',
        ]);
    }
}
