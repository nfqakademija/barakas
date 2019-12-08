<?php


namespace App\Service;

use App\Entity\Message;
use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class DormitoryService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function calculateRewardPoints(DateTime $created_at, int $maxPoints): int
    {
        $currentTime =  new DateTime();
        $currentTime = $currentTime->getTimestamp();
        $created_at = $created_at->getTimestamp();
        $minutes = ($currentTime-$created_at)/60;
        $minutes = intval($minutes);
        $points  = $maxPoints - $minutes;
        if ($points<=0) {
            return 1;
        }

        return $points;
    }

    public function canSendMessage(User $user): bool
    {
        $messageRepo = $this->entityManager->getRepository(Message::class);
        $lastMessage = $messageRepo->findBy(['user' => $user->getId()], array('created_at'=>'DESC'), 1);

        if (!empty($lastMessage[0])) {
            if ($lastMessage[0]->getCreatedAt() > new \DateTime('2 minutes ago')) {
                return false;
            }
        }
        return true;
    }

    public function getAllLoggedInUsers(User $user)
    {
        $studentsRepo = $this->entityManager->getRepository(User::class);
        $delay = new \DateTime('2 minutes ago');
        $expression = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where(
            $expression->gt('lastActivityAt', $delay)
        )
            ->andWhere($expression->eq('dorm_id', $user->getDormId()));
        $criteria->orderBy(['lastActivityAt' => Criteria::DESC]);
        return $loggedInUsers = $studentsRepo->matching($criteria);
    }
}
