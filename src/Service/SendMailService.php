<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @param array<User|string> $context
     */
    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {
        // Create mail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        // Send mail
        $this->mailer->send($email);
    }
}
