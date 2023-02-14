<?php

namespace App\Security;

use App\Entity\User as AppUser;
use App\Service\JWTService;
use App\Service\SendMailService;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private ContainerBagInterface $params,
        private JWTService $jwt,
        private SendMailService $mail)
    {
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->getIsVerified()) {
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
            $token = $this->jwt->generate($header, $payload, $this->params->get('security.jwtsecret'));

            // Resend activate mail
            $this->mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activate your account',
                'register',
                compact('user', 'token')
            );

            // the message passed to this exception is meant to be displayed to the user
            throw new CustomUserMessageAccountStatusException('Your account is not verified. Check your email "Activate your account '.$user->getEmail().'"');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }
}
