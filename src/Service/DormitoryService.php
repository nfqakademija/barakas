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

    protected function getHelpRepository()
    {
        return $this->entityManager->getRepository(Help::class);
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

    public function provideHelp($id)
    {
        $user = $this->getUser();
        $message = $this->getMessagesRepository()->find($id);
        $dormitory = $this->getDormitoryRepository()->getLoggedInUserDormitory($user->getDormId());
        $help = $this->getHelpRepository()->findUserProvidedHelp(
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
        $message->setSolver($user);

        $this->entityManager->flush();

        return true;
    }

    public function reportMessage($id)
    {
        $message = $this->getMessagesRepository()->find($id);

        if (!$message) {
            return false;
        }

        $message->setReported(true);
        $this->entityManager->flush();

        return true;
    }

    public function acceptHelpRequest($helpId, $messageId)
    {
        $message = $this->getMessagesRepository()->find($messageId);
        $userWhoHelped = $this->getHelpRepository()->findUserWhoProvidedHelp($helpId);
        $userWhoHelpedPoints = $userWhoHelped->getPoints();
        $newPoints = $userWhoHelpedPoints + $this->
            calculateRewardPoints($message->getCreatedAt(), 500);

        $userWhoHelped->setPoints($newPoints);

        $help = $this->getHelpRepository()->find($helpId);

        $this->entityManager->remove($help);
        $this->entityManager->flush();

        return true;
    }

    public function denyHelpRequest($id)
    {
        $helpRepository = $this->getHelpRepository();

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

    public function getDormitoryWithStudents($user)
    {
        $dormitoryRepo = $this->getDormitoryRepository();

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

    public function postNewMessage($data)
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
