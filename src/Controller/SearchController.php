<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class SearchController extends AbstractController
{
    #[Route('/search', name: 'app.search', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Request $request,
        PaginatorInterface $paginator
        ): Response {
        $search = $request->query->get('q');

        if (!empty($search)) {
            $pagination = $paginator->paginate(
                /* @phpstan-ignore-next-line */
                $postRepository->findBySearch($search), /* query NOT result */
                $request->query->getInt('page', 1), /* page number */
                8 /* limit per page */
            );
        }

        return $this->render('search/index.html.twig', [
            'posts' => $pagination ?? null,
            'query' => $search,
        ]);
    }
}
