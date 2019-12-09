<?php


namespace App\Service;

use App\Entity\Dormitory;
use App\Entity\Help;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\SolvedType;
use App\Entity\StatusType;
use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Security\Core\Security;

class DormitoryService
{
    private $entityManager;
    private $security;
    private $studentManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        StudentManager $studentManager
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->studentManager = $studentManager;
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
        $messageRepo = $this->getRepository(Message::class);
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
        $studentsRepo = $this->getRepository(User::class);
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

    private function getRepository(string $entity)
    {
        return $this->entityManager->getRepository($entity);
    }

    public function getDormitory()
    {
        return $this->getRepository(Dormitory::class)->getLoggedInUserDormitory($this->getUser()->getDormId());
    }

    protected function getUser()
    {
        return $this->security->getUser();
    }

    public function getStudents()
    {
        return $this->getRepository(Dormitory::class)->orderTopStudentsByPoints($this->getUser()->getDormId());
    }

    public function getMessages()
    {
        return $this->getRepository(Dormitory::class)->getDormitoryMessages($this->getUser()->getDormId());
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

    public function saveMessage(string $content): Message
    {
        $message = new Message();
        $message->setUser($this->getUser());
        $message->setDormId($this->getUser()->getDormId());
        $message->setRoomNr($this->getUser()->getRoomNr());
        $message->setContent($content);
        $message->setStatus(StatusType::posted());
        $message->setSolved(SolvedType::notSolved());
        $message->setPoints(500);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
        return $message;
    }

    public function findMessage(int $id)
    {
        $repository = $this->getRepository(Message::class);
        return $repository->find($id);
    }

    public function getLoggedInUserDormitory()
    {
        $repository = $this->getRepository(Dormitory::class);
        return $repository->getLoggedInUserDormitory($this->getUser()->getDormId());
    }

    public function getStudentsInDormitory()
    {
        $repository = $this->getRepository(Dormitory::class);
        return $repository->getStudentsInDormitory($this->getUser()->getDormId());
    }

    public function provideHelp(int $id): bool
    {
        $user = $this->getUser();
        $message = $this->getRepository(Message::class)->find($id);
        $dormitory = $this->getRepository(Dormitory::class)->getLoggedInUserDormitory($user->getDormId());
        $help = $this->getRepository(Help::class)->findUserProvidedHelp(
            $message->getUser()->getId(),
            $user->getId(),
            $message
        );

        if (!$message || $help) {
            return false;
        }

        $help = new Help();
        $help->setMessage($message);
        $help->setUser($user);
        $help->setDormId($dormitory->getId());
        $help->setRoomNr($user->getRoomNr());
        $help->setRequesterId($message->getUser()->getId());

        $this->entityManager->persist($help);
        $message->setSolved(SolvedType::solved());
        $message->setStatus(StatusType::pending());
        $message->setPoints($this->calculateRewardPoints($message->getCreatedAt(), 500));
        $message->setSolver($user);

        $this->entityManager->flush();

        return true;
    }

    public function reportMessage(int $id): bool
    {
        $message = $this->getRepository(Message::class)->find($id);

        if (!$message) {
            return false;
        }

        $message->setReported(true);
        $this->entityManager->flush();

        return true;
    }

    public function acceptHelpRequest(int $helpId, int $messageId): bool
    {
        $message = $this->getRepository(Message::class)->find($messageId);
        $userWhoHelped = $this->getRepository(Help::class)->findUserWhoProvidedHelp($helpId);
        $userWhoHelpedPoints = $userWhoHelped->getPoints();
        $newPoints = $userWhoHelpedPoints + $this->
            calculateRewardPoints($message->getCreatedAt(), 500);

        $userWhoHelped->setPoints($newPoints);
        $message->setStatus(StatusType::approved());

        $help = $this->getRepository(Help::class)->find($helpId);

        $this->entityManager->remove($help);
        $this->entityManager->flush();

        return true;
    }

    public function denyHelpRequest(int $id): bool
    {
        $helpRepository = $this->getRepository(Help::class);

        $message = $helpRepository->findMessageFromHelp($id);
        $message->setSolved(SolvedType::notSolved());
        $message->setSolver(null);

        $help = $helpRepository->find($id);

        if (!$help) {
            return false;
        }

        $this->entityManager->remove($help);
        $this->entityManager->flush();

        return true;
    }

    public function getDormitoryWithStudents(User $user)
    {
        $dormitoryRepo = $this->getRepository(Dormitory::class);

        $dormitory = $dormitoryRepo->getLoggedInUserDormitory($user->getDormId());
        $students = $dormitoryRepo->orderAllStudentsByPoints($user->getDormId());

        if (!$dormitory) {
            return false;
        }

        return array('dormitory' => $dormitory, 'students' => $students);
    }

    public function getDormitoryInfo()
    {
        $dormitory = $this->getDormitory();
        $students = $this->getStudents();
        $messages = $this->getMessages();

        if (!$dormitory) {
            return false;
        }

        return array('dormitory' => $dormitory, 'students' => $students, 'messages' => $messages);
    }

    public function postNewMessage($data): bool
    {
        if (!$data) {
            return false;
        }

        $message = $this->saveMessage($data->getContent());
        $students = $this->getDormitoryInfo();

        $students = $this->studentManager->removeStudentFromStudentsArray($students['students']);

        $this->saveNotifications($students, $message);

        return true;
    }
}
