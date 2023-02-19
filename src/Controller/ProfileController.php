<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/profile')]
class ProfileController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app.profile.index', methods: ['GET', 'POST'])]
    public function index(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!is_null($form->get('password')->getData())) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }
            $userRepository->save($user, true);

            // return $this->redirectToRoute('app.profile.index', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
