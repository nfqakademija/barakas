<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\NamedAddress;
use \Symfony\Component\Mailer\MailerInterface;

class EmailService
{
    private $mailer;
    private $serverEmail;

    public function __construct(MailerInterface $mailer, string $serverEmail)
    {
        $this->mailer = $mailer;
        $this->serverEmail = $serverEmail;
    }

    public function sendOrganisationSignupMail(
        string $organisationEmail,
        string $organisationPassword
    ) {

        $serverEmail = $this->serverEmail;

        $email = (new TemplatedEmail())
            ->from(new NamedAddress($serverEmail, $this->getName()))
            ->to($organisationEmail)
            ->subject('Organizacijos paskyra sukurta')
            ->htmlTemplate('email/org_signup.html.twig')
            ->context([
                'org_email' => $organisationEmail,
                'password' => $organisationPassword
            ]);

        return $this->mailer->send($email);
    }

    private function getName()
    {
        return 'Kaimyne padėk!';
    }

}
