<?php


namespace App\Service;

use App\Entity\Academy;
use App\Entity\AcademyType;
use App\Entity\Dormitory;
use App\Entity\Help;
use App\Entity\Invite;
use App\Entity\Notification;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService extends Service
{

    public function insertDormitory(Dormitory $dormitory)
    {
        $dormitory->setAddress($dormitory->getAddress());
        $dormitory->setOrganisationId($this->getUser()->getId());
        $dormitory->setTitle($dormitory->getTitle());

        $this->entityManager->persist($dormitory);
        $this->entityManager->flush();
    }

    public function getUserDormitories()
    {
        $dormitoryRepository = $this->getRepository(Dormitory::class);
        return $dormitoryRepository->getUserDormitories($this->getUser()->getId());
    }

    public function getUniversities()
    {
        $academyRepository = $this->getRepository(Academy::class);
        return $academyRepository->findBy(['academyType' => AcademyType::university()->id()]);
    }

    public function getColleges()
    {
        $academyRepository = $this->getRepository(Academy::class);
        return $academyRepository->findBy(['academyType' => AcademyType::college()->id()]);
    }

    public function insertOrganisation(User $organisation): User
    {
        $plainPassword = $organisation->generateRandomPassword();
        $encodedPassword = $this->encoder->encodePassword($organisation, $plainPassword);
        $organisation->setPassword($encodedPassword);
        $organisation->setRoles(array('ROLE_ADMIN'));
        $organisation->setPoints(0);

        $this->entityManager->persist($organisation);
        $this->entityManager->flush();

        $this->emailService->sendOrganisationSignupMail($organisation->getEmail(), $plainPassword);
        return $organisation;
    }

    public function changePassword($data, UserInterface $user): void
    {
        $newPassword = $this->encoder->encodePassword($user, $data['password']);
        $user->setPassword($newPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function insertStudentAccount(User $student, String $invite): void
    {
        $invitation = $this
            ->getRepository(Invite::class)
            ->findOneBy(array('url' => $invite));

        $plainPassword = $student->getPassword();
        $encodedPassword = $this->encoder->encodePassword($student, $plainPassword);
        $student->setOwner($invitation->getName());
        $student->setEmail($invitation->getEmail());
        $student->setPassword($encodedPassword);
        $student->setDormId($invitation->getDorm());
        $student->setRoomNr($invitation->getRoom());
        $student->setRoles(array('ROLE_USER'));
        $student->setPoints(0);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        $this->entityManager->remove($invitation);
        $this->entityManager->flush();
    }

    public function getNotificationsByUser(): Array
    {
        $user = $this->getUser();
        $notificationRepo = $this->getRepository(Notification::class);
        return $notificationRepo->getNotificationsByUser($user->getId());
    }

    public function deleteAll($notifications): void
    {
        foreach ($notifications as $notification) {
            $this->entityManager->remove($notification);
        }
        $this->entityManager->flush();
    }

    public function getHelpMessages()
    {
        $user = $this->getUser();
        $helpRepo = $this->getRepository(Help::class);
        return $helpRepo->userProblemSolvers($user->getId());
    }
}
