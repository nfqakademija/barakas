<?php

namespace App\Twig;

use App\Entity\Help;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $entityManager;
    private $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('notifications', [$this, 'getNotifications']),
            new TwigFunction('helpMessages', [$this, 'getHelpMessages']),
        ];
    }

    public function getNotifications()
    {
        $user = $this->security->getUser();

        $notificationRepo = $this->entityManager->getRepository(Notification::class);
        $notifications = $notificationRepo->getNotificationsByUser($user->getId());

        return $notifications;
    }

    public function getHelpMessages()
    {
        $user = $this->security->getUser();

        $helpRepo = $this->entityManager->getRepository(Help::class);
        $helpMessages = $helpRepo->userProblemSolvers($user->getId());

        return $helpMessages;
    }
}
