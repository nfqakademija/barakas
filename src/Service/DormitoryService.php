<?php


namespace App\Service;

use App\Entity\Dormitory;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\SolvedType;
use App\Entity\StatusType;
use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DormitoryService
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
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

    public function canSendMessage(): bool
    {
        $user = $this->security->getUser();
        $messageRepo = $this->getMessagesRepository();
        $lastMessage = $messageRepo->findBy(['user' => $user->getId()], array('created_at'=>'DESC'), 1);

        if (!empty($lastMessage[0])) {
            if ($lastMessage[0]->getCreatedAt() > new \DateTime('2 minutes ago')) {
                return false;
            }
        }
        return true;
    }

    public function getAllLoggedInUsers()
    {
        $user = $this->security->getUser();
        $studentsRepo = $this->getUserRepository();
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

    public function getDormitory()
    {

        return $this->getDormitoryRepository()->getLoggedInUserDormitory($this->getUser()->getDormId());
    }

    protected function getDormitoryRepository()
    {
        return $this->entityManager->getRepository(Dormitory::class);
    }

    protected function getMessagesRepository()
    {
        return $this->entityManager->getRepository(Message::class);
    }

    protected function getUserRepository()
    {
        return $this->entityManager->getRepository(User::class);
    }

    protected function getUser()
    {
        return $this->security->getUser();
    }

    public function getStudents()
    {
        return $this->getDormitoryRepository()->orderTopStudentsByPoints($this->getUser()->getDormId());
    }

    public function getMessages()
    {
        return $this->getDormitoryRepository()->getDormitoryMessages($this->getUser()->getDormId());
    }

    public function saveNotifications(Array $students, Message $message)
    {
        foreach ($students as $student) {
            $notification = new Notification();
            $notification->setUser($message->getUser());
            $notification->setCreatedAt(new \DateTime());
            $notification->setRoomNr($message->getRoomNr());
            $notification->setDormId($message->getDormId());
            $notification->setContent($message->getContent());
            $notification->setRecipientId($student->getId());
            $notification->setMessage($message);
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }
    }

    public function saveMessage($content)
    {
        $message = new Message();
        $message->setUser($this->getUser());
        $message->setDormId($this->getUser()->getDormId());
        $message->setRoomNr($this->getUser()->getRoomNr());
        $message->setContent($content);
        $message->setStatus(StatusType::urgent());
        $message->setSolved(SolvedType::notSolved());

        $this->entityManager->persist($message);
        $this->entityManager->flush();
        return $message;
    }

    public function findMessage(int $id)
    {
        $repository = $this->getMessagesRepository();
        return $repository->find($id);
    }
    public function getLoggedInUserDormitory()
    {
        $repository = $this->getDormitoryRepository();
        return $repository->getLoggedInUserDormitory($this->getUser()->getDormId());
    }

    public function getStudentsInDormitory()
    {
        $repository = $this->getDormitoryRepository();
        return $repository->getStudentsInDormitory($this->getUser()->getDormId());
    }
}
