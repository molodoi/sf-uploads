<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgetPasswordType;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Security\MyLoginFormAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'security.login')]
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app.home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'security.logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'security.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, MyLoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            // inject in this method the UserAuthenticatorInterface $userAuthenticator
            // return $userAuthenticator->authenticateUser(
            //     $user,
            //     $authenticator,
            //     $request
            // );
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];

            // Create Payload
            $payload = [
                'user_id' => $user->getId(),
                'user_email' => $user->getEmail(),
            ];

            // Generate token
            $token = $jwt->generate($header, $payload, $this->getParameter('security.jwtsecret'));

            // Send activate mail
            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activate your account',
                'register',
                compact('user', 'token')
            );

            return $this->redirectToRoute('security.login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/check/{token}', name: 'check_valid_register_token')]
    public function verifyUser(string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        // We check if the token is valid, has not expired and has not been modified
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('security.jwtsecret'))) {
            // Retrieve payload
            $payload = $jwt->getPayload($token);

            // Retrieve user from token
            $user = $userRepository->find($payload['user_id']);

            // We check that the user exists and has not yet activated his account
            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Account activated');

                return $this->redirectToRoute('security.login');
            }
        }
        $this->addFlash('danger', 'Token invalid or expired');

        return $this->redirectToRoute('security.login');
    }

    #[Route('/forget-password', name: 'security.forget_password')]
    public function forgottenPassword(Request $request,
    UserRepository $userRepository,
    TokenGeneratorInterface $tokenGenerator,
    EntityManagerInterface $entityManager,
    SendMailService $mail): Response
    {
        $form = $this->createForm(ForgetPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @phpstan-ignore-next-line */
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            if ($user) {
                // Generate reset token
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // Reset password link
                $url = $this->generateUrl('security.reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // compact datas for mail
                $context = compact('url', 'user');

                // Send reset mail
                $mail->send(
                    'no-reply@e-commerce.fr',
                    $user->getEmail(),
                    'Reset password',
                    'reset_password',
                    $context
                );

                $this->addFlash('success', 'Email send successfully');

                return $this->redirectToRoute('security.login');
            }
            // $user is null
            $this->addFlash('danger', 'A problem has occurred');

            return $this->redirectToRoute('security.login');
        }

        return $this->render('security/forget_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'security.reset_password')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Check if token is on database
        $user = $userRepository->findOneBy(['reset_token' => $token]);

        // Check if user existe
        if ($user) {
            $form = $this->createForm(ResetPasswordType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Set empty token
                $user->setResetToken('');

                // Set new hashing password
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Password successfully changed');

                return $this->redirectToRoute('security.login');
            }

            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // If token is invalid redirect to login page
        $this->addFlash('danger', 'Invalid token');

        return $this->redirectToRoute('security.login');
    }
}
