<?php
namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    public function recentPosts(PostRepository $postRepository, int $max = 5): Response
    {
         return $this->render('home/_recent_posts.html.twig', [
            'posts' => $postRepository->getLastPosts()
        ]);
    }
}