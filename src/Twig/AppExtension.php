<?php

namespace App\Twig;

use App\Entity\Help;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $entityManager;
    private $security;
    private $params;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        ParameterBagInterface $params
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->params = $params;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('notifications', [$this, 'getNotifications']),
            new TwigFunction('helpMessages', [$this, 'getHelpMessages']),
            new TwigFunction('link', [$this, 'getMercureLink']),
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

    public function getMercureLink()
    {
        return $link = $this->params->get('mercureUrl');
    }
}
