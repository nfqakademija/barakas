<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class Service
{
    protected $entityManager;
    protected $security;
    protected $emailService;
    protected $studentManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        EmailService $emailService,
        StudentManager $studentManager
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->studentManager = $studentManager;
        $this->emailService = $emailService;
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
