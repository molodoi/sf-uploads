<?php

/*
 * This file is part of the Symfony package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\ImageRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app.post.index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            /* @phpstan-ignore-next-line */
            $postRepository->getAllPostsByUserQuery($this->getUser()), /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            8 /* limit per page */
        );

        return $this->render('post/index.html.twig', [
            'posts' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app.post.new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash(
                'success',
                'Post was created !'
            );
            /* @phpstan-ignore-next-line */
            $post->setUser($this->getUser());
            $postRepository->save($post, true);

            return $this->redirectToRoute('app.post.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app.post.show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app.post.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->save($post, true);

            return $this->redirectToRoute('app.post.edit', ['slug' => $post->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app.post.delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), (string) $request->request->get('_token'))) {
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app.post.index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/image/{image_id}', name: 'app.post.image.delete', methods: ['GET'], requirements: ['id' => '\d+', 'image_id' => '\d+'])]
    public function deleteImage(Request $request, Post $post, ImageRepository $imageRepo,
    EntityManagerInterface $entityManager, ): RedirectResponse
    {
        /** App\Entity\Image $image */
        $image = $imageRepo->find($request->get('image_id'));
        if (
            $request->get('id') === (string) $post->getId()
            && $image->getPost()->getId() === $post->getId()
        ) {
            $post->removeImage($image);
            $entityManager->persist($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app.post.edit', ['slug' => $post->getSlug()]);
    }
}
