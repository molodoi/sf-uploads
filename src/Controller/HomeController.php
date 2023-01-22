<?php

/*
 * This file is part of the Symfony package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app.home')]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $postRepository->getAllPostsQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            6 /* limit per page */
        );

        return $this->render('home/index.html.twig', [
            'posts' => $pagination,
        ]);
    }

    public function recentPosts(PostRepository $postRepository, int $max = 5): Response
    {
        return $this->render('home/_recent_posts.html.twig', [
           'posts' => $postRepository->getLastPosts(),
        ]);
    }
}
