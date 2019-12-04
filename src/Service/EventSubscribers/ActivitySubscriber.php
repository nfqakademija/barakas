<?php
namespace App\Service\EventSubscribers;

use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ActivitySubscriber implements EventSubscriberInterface
{

    private $em;
    private $security;

    public function __construct(
        EntityManagerInterface $em,
        Security $security
    ) {
        $this->em = $em;
        $this->security = $security;
    }

    public function onTerminate()
    {
        $user = $this->security->getUser();

        if (!$user->isActiveNow()) {
            $user->setLastActivityAt(new DateTime());
            $this->em->persist($user);
            $this->em->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::TERMINATE => [['onTerminate', 20]],
        ];
    }
}
