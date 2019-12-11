<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class Service
{
    protected $entityManager;
    protected $security;
    protected $emailService;
    protected $encoder;
    protected $bus;
    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        EmailService $emailService,
        UserPasswordEncoderInterface $encoder,
        MessageBusInterface $bus,
        UrlGeneratorInterface $router
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->emailService = $emailService;
        $this->encoder = $encoder;
        $this->bus = $bus;
        $this->router = $router;
    }

    protected function getRepository(string $entity)
    {
        return $this->entityManager->getRepository($entity);
    }

    protected function getUser()
    {
        return $this->security->getUser();
    }
}
