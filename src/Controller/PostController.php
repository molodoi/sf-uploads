<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]

class PostController extends AbstractController
{
    #[Route('/', name: 'app.post.index')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/create', name: 'app.post.create')]
    public function create(): Response
    {
        return $this->render('post/create.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
