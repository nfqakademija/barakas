<?php


namespace App\Service;

use App\Entity\ApprovedType;
use App\Entity\Dormitory;
use App\Entity\DormitoryChange;
use App\Entity\Invite;
use App\Entity\RoomChange;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class AdminService
{
    private $entityManager;
    private $emailService;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        EmailService $emailService,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
        $this->security = $security;
    }

    private function getRepository($entity)
    {
        return $this->entityManager->getRepository($entity);
    }


    public function getDormitory($id)
    {
        $dormitoryRepository = $this->getRepository(Dormitory::class);
        return $dormitoryRepository->find($id);
    }

    public function getOrganisationDormitory($id)
    {
        $dormitoryRepo = $this->getRepository(Dormitory::class);
        $dormitory = $this->getDormitory($id);
        return $dormitoryRepo->findOrganisationDormitory($dormitory->getOrganisationId());
    }

    public function indexPage($id)
    {
        $dormitoryInfo = $this->getDormitory($id);

        $dormitoryRepository = $this->getRepository(Dormitory::class);
        $invitesRepository = $this->getRepository(Invite::class);
        $studentsRepository = $this->getRepository(User::class);

        $invites = $invitesRepository->getInvitations($id);
        $students = $studentsRepository->getStudents($id);

        $organisationID = $dormitoryInfo->getOrganisationId();

        $dormitoryOrganisation = $dormitoryRepository->findOrganisationDormitory($organisationID);

        if (!$dormitoryInfo) {
            return false;
        }

        return array('students' => $students, 'invites' => $invites, 'dormitoryInfo' => $dormitoryInfo,
            'dormitoryOrganisation' => $dormitoryOrganisation);
    }

    public function addNewStudentToDormitory($formData, $invitation, $dormitory)
    {
        $studentsRepository = $this->getRepository(User::class);

        $url = $invitation->generateUrl();
        $invitation->setName($formData->getName());
        $invitation->setEmail($formData->getEmail());
        $invitation->setRoom($formData->getRoom());
        $invitation->setUrl($url);
        $invitation->setDorm($dormitory->getId());

        $studentExists = $studentsRepository->findByEmail($formData->getEmail());

        if ($studentExists) {
            return false;
        }

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();
        return $this->emailService->sendInviteMail($invitation->getEmail(), $url, $invitation->getName());
    }

    public function getDormitoryChangeRequests()
    {
        $user = $this->security->getUser();
        $requestsRepo = $this->getRepository(DormitoryChange::class);
        return $requestsRepo->getNotApprovedRequests($user);
    }

    public function getRoomChangeRequests()
    {
        $user = $this->security->getUser();
        $requestsRepo = $this->getRepository(RoomChange::class);
        return $requestsRepo->findNotApprovedRequests($user);
    }

    public function approveDormitoryChangeRequest($id)
    {
        $requestRepo = $this->getRepository(DormitoryChange::class);
        $request = $requestRepo->find($id);

        if (!$request) {
            return false;
        }

        $user = $request->getUser();

        $request->setApproved(ApprovedType::approved());
        $user->setDormId($request->getDormitory()->getId());
        $user->setRoomNr($request->getRoomNr());

        $this->entityManager->flush();
        return true;
    }

    public function removeDormitoryChangeRequest($id)
    {
        $requestRepo = $this->getRepository(DormitoryChange::class);
        $request = $requestRepo->find($id);

        if (!$request) {
            return false;
        }

        $this->entityManager->remove($request);
        $this->entityManager->flush();
        return true;
    }

    public function approveRoomChangeRequest($id)
    {
        $requestRepo = $this->getRepository(RoomChange::class);
        $request = $requestRepo->find($id);

        if (!$request) {
            return false;
        }

        $user = $request->getUser();
        $request->setApproved(ApprovedType::approved());
        $user->setRoomNr($request->getNewRoomNr());

        $this->entityManager->flush();
        return true;
    }

    public function removeRoomChangeRequest($id)
    {
        $requestRepo = $this->getRepository(RoomChange::class);
        $request = $requestRepo->find($id);

        if (!$request) {
            return false;
        }

        $this->entityManager->remove($request);
        $this->entityManager->flush();
        return true;
    }
}
