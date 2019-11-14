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

    public function sendInviteMail(
        string $email,
        string $link,
        string $name
    ) {

        $serverEmail = $this->serverEmail;

        $email = (new TemplatedEmail())
            ->from(new NamedAddress($serverEmail, $this->getName()))
            ->to($email)
            ->subject('Jūs pakviestas į sistemą Kaimyne padėk!')
            ->htmlTemplate('email/invite.html.twig')
            ->context([
                'name' => $name,
                'link' => $link
            ]);

        return $this->mailer->send($email);
    }

    private function getName()
    {
        return 'Kaimyne padėk!';
    }
}
