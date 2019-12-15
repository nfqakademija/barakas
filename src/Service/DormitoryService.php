<?php


namespace App\Service;

use App\Entity\AchievementType;
use App\Entity\Award;
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
use Exception;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DormitoryService extends Service
{
    private $achievementService;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        EmailService $emailService,
        UserPasswordEncoderInterface $encoder,
        AchievementService $achievementService,
        MessageBusInterface $bus,
        UrlGeneratorInterface $router
    ) {
        parent::__construct($entityManager, $security, $emailService, $encoder, $bus, $router);
        $this->achievementService = $achievementService;
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

    public function getDormitory()
    {
        return $this->getRepository(Dormitory::class)->getLoggedInUserDormitory($this->getUser()->getDormId());
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
        $messagesRepo = $this->getRepository(Message::class);

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

        $userMessages = $messagesRepo->getUserMessages($this->getUser());
        $messagesAchievements = $this->getMessagesAchievements($this->getUser());

        if (!$messagesAchievements['tenMessagesAchievement'] && count($userMessages) === 10) {
            $this->achievementService->giveTenMessagesAchievement($this->getUser());
        }

        if (!$messagesAchievements['twentyMessagesAchievement'] && count($userMessages) === 20) {
            $this->achievementService->giveTwentyMessagesAchievement($this->getUser());
        }

        if (!$messagesAchievements['thirtyMessagesAchievement'] && count($userMessages) === 30) {
            $this->achievementService->giveThirtyMessagesAchievement($this->getUser());
        }

        return $message;
    }

    private function getMessagesAchievements($user): array
    {
        $awardsRepo = $this->getRepository(Award::class);
        $tenMessagesAchievement = $awardsRepo->findTenMessagesAchievementByUser($user);
        $twentyMessagesAchievement = $awardsRepo->findTwentyMessagesAchievementByUser($user);
        $thirtyMessagesAchievement = $awardsRepo->findThirtyMessagesAchievementByUser($user);

        return array('tenMessagesAchievement' => $tenMessagesAchievement,
            'twentyMessagesAchievement' => $twentyMessagesAchievement,
            'thirtyMessagesAchievement' => $thirtyMessagesAchievement);
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

    public function provideHelp(int $id): void
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
            throw new Exception('Message or user was not found');
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
    }

    public function reportMessage(int $id): void
    {
        $message = $this->getRepository(Message::class)->find($id);

        if (!$message) {
            throw new Exception('Message not found.');
        }

        $message->setReported(true);
        $this->entityManager->flush();
    }

    public function acceptHelpRequest(int $helpId, int $messageId): void
    {
        $message = $this->getRepository(Message::class)->find($messageId);

        if (!$message) {
            throw new Exception('Message not found.');
        }

        $userWhoHelped = $this->getRepository(Help::class)->findUserWhoProvidedHelp($helpId);

        if (!$userWhoHelped) {
            throw new Exception('User who provided help was not found.');
        }

        $userWhoHelpedPoints = $userWhoHelped->getPoints();
        $newPoints = $userWhoHelpedPoints + $this->
            calculateRewardPoints($message->getCreatedAt(), 500);

        $userWhoHelped->setPoints($newPoints);
        $message->setStatus(StatusType::approved());

        $help = $this->getRepository(Help::class)->find($helpId);

        $this->entityManager->remove($help);
        $this->entityManager->flush();

        $messagesRepo = $this->getRepository(Message::class);
        $helps = $messagesRepo->getUserSolvedProblems($userWhoHelped);
        $helpAchievements = $this->getHelpAchievements($userWhoHelped);

        if (!$helpAchievements['firstAidAchievement'] && count($helps) === 1) {
            $this->achievementService->giveFirstAidAchievement($userWhoHelped);
        }
        if (!$helpAchievements['tenHelpsAchievement'] && count($helps) === 10) {
            $this->achievementService->giveTenHelpAchievement($userWhoHelped);
        }
        if (!$helpAchievements['twentyHelpsAchievement'] && count($helps) === 20) {
            $this->achievementService->giveTwentyHelpAchievement($userWhoHelped);
        }
        if (!$helpAchievements['oneThousandPointsAchievement'] && $newPoints >= 1000 && $newPoints < 2500) {
            $this->achievementService->giveOneThousandPointsAchievement($userWhoHelped);
        }
        if (!$helpAchievements['twoThousandPointsAchievement'] && $newPoints >= 2500 && $newPoints < 5000) {
            $this->achievementService->giveTwoThousandPointsAchievement($userWhoHelped);
        }
        if (!$helpAchievements['fiveThousandPointsAchievement'] && $newPoints >= 5000 && $newPoints < 10000) {
            $this->achievementService->giveFiveThousandPointsAchievement($userWhoHelped);
        }
        if (!$helpAchievements['tenThousandPointsAchievement'] && $newPoints >= 10000) {
            $this->achievementService->giveTenThousandPointsAchievement($userWhoHelped);
        }
    }

    private function getHelpAchievements($userWhoHelped): array
    {
        $awardsRepo = $this->getRepository(Award::class);
        $firstAidAchievement = $awardsRepo->findFirstAidAchievementByUser($userWhoHelped);
        $tenHelpsAchievement = $awardsRepo->findTenHelpAchievementByUser($userWhoHelped);
        $twentyHelpsAchievement = $awardsRepo->findTwentyHelpAchievementByUser($userWhoHelped);
        $oneThousandPointsAchievement = $awardsRepo->findOneThousandPointsAchievementByUser($userWhoHelped);
        $twoThousandPointsAchievement = $awardsRepo->findTwoThousandPointsAchievementByUser($userWhoHelped);
        $fiveThousandPointsAchievement = $awardsRepo->findFiveThousandPointsAchievementByUser($userWhoHelped);
        $tenThousandPointsAchievement = $awardsRepo->findTenThousandPointsAchievementByUser($userWhoHelped);

        return array('firstAidAchievement' => $firstAidAchievement, 'tenHelpsAchievement' => $tenHelpsAchievement,
            'twentyHelpsAchievement' => $twentyHelpsAchievement,
            'oneThousandPointsAchievement' => $oneThousandPointsAchievement,
            'twoThousandPointsAchievement' => $twoThousandPointsAchievement,
            'fiveThousandPointsAchievement' => $fiveThousandPointsAchievement,
            'tenThousandPointsAchievement' => $tenThousandPointsAchievement);
    }

    public function denyHelpRequest(int $id): void
    {
        $helpRepository = $this->getRepository(Help::class);
        $message = $helpRepository->findMessageFromHelp($id);

        if (!$message) {
            throw new Exception('Message not found.');
        }

        $message->setSolved(SolvedType::notSolved());
        $message->setSolver(null);

        $help = $helpRepository->find($id);

        if (!$help) {
            throw new Exception('Help request not found.');
        }

        $this->entityManager->remove($help);
        $this->entityManager->flush();
    }

    public function getDormitoryWithStudents($user): array
    {
        $dormitoryRepo = $this->getRepository(Dormitory::class);

        $dormitory = $dormitoryRepo->getLoggedInUserDormitory($user->getDormId());

        if (!$dormitory) {
            throw new Exception('Dormitory not found.');
        }

        $students = $dormitoryRepo->orderAllStudentsByPoints($user->getDormId());

        if (!$students) {
            throw new Exception('Students not found.');
        }

        return array('dormitory' => $dormitory, 'students' => $students);
    }

    public function getDormitoryInfo(): array
    {
        $dormitory = $this->getDormitory();
        if (!$dormitory) {
            throw new Exception('Dormitory not found.');
        }
        $students = $this->getStudents();

        $messages = $this->getMessages();

        return array('dormitory' => $dormitory, 'students' => $students, 'messages' => $messages);
    }

    public function postNewMessage($data): void
    {
        if (!$data) {
            throw new Exception('No data was provided.');
        }

        $message = $this->saveMessage($data->getContent());
        $students = $this->getDormitoryInfo();

        $students = $this->removeStudentFromStudentsArray($students['students']);

        $this->saveNotifications($students, $message);
        $this->pushMessage($message);
    }

    private function pushMessage(Message $message)
    {

        $message = htmlspecialchars($message);
        $update = new Update(
            [
                $this->router->generate(
                    'dormitory',
                    ['id' => $this->getUser()->getDormId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $this->router->generate('rules', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->router->generate('my-messages', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->router->generate('acceptHelp', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->router->generate('contacts', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->router->generate('passwordChange', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->router->generate('change_dormitory', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->router->generate('change_room', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->router->generate('achievements', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->router->generate('dormitory_leaderboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
            json_encode([
                'content' => $message->getContent(),
                'owner' => $this->getUser()->getOwner(),
                'id' => $message->getId(),
                'room' => $this->getUser()->getRoomNr()
            ])
        );
        $this->bus->dispatch($update);
    }

    public function removeStudentFromStudentsArray($students)
    {
        $studentToRemove = null;

        foreach ($students as $struct) {
            if ($this->getUser()->getOwner() == $struct->getOwner()) {
                $studentToRemove = $struct;
                break;
            }
        }

        $key = array_search($studentToRemove, $students);
        unset($students[$key]);

        return $students;
    }
}
