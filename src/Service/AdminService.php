<?php


namespace App\Service;

use App\Entity\ApprovedType;
use App\Entity\Dormitory;
use App\Entity\DormitoryChange;
use App\Entity\Invite;
use App\Entity\Message;
use App\Entity\RoomChange;
use App\Entity\User;

class AdminService extends Service
{
    public function getDormitory(int $id)
    {
        $dormitoryRepository = $this->getRepository(Dormitory::class);
        return $dormitoryRepository->find($id);
    }

    public function getOrganisationDormitory(int $id)
    {
        $dormitoryRepo = $this->getRepository(Dormitory::class);
        $dormitory = $this->getDormitory($id);
        if (!$dormitory) {
            throw new \Exception('Dormitory not found.');
        }
        return $dormitoryRepo->findOrganisationDormitory($dormitory->getOrganisationId());
    }

    public function indexPage(int $id): array
    {
        $dormitoryInfo = $this->getDormitory($id);

        if (!$dormitoryInfo) {
            throw new \Exception('Dormitory not found.');
        }

        $dormitoryRepository = $this->getRepository(Dormitory::class);
        $invitesRepository = $this->getRepository(Invite::class);
        $studentsRepository = $this->getRepository(User::class);

        $invites = $invitesRepository->getInvitations($id);
        $students = $studentsRepository->getStudents($id);

        $organisationID = $dormitoryInfo->getOrganisationId();
        $dormitoryOrganisation = $dormitoryRepository->findOrganisationDormitory($organisationID);


        return array('students' => $students, 'invites' => $invites, 'dormitoryInfo' => $dormitoryInfo,
            'dormitoryOrganisation' => $dormitoryOrganisation);
    }

    public function addNewStudentToDormitory($formData, $invitation, $dormitory): bool
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
        $this->emailService->sendInviteMail($invitation->getEmail(), $url, $invitation->getName());
        return true;
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

    public function approveDormitoryChangeRequest(int $id): void
    {
        $requestRepo = $this->getRepository(DormitoryChange::class);
        $request = $requestRepo->find($id);

        if (!$request) {
            throw new \Exception('Request not found.');
        }

        $user = $request->getUser();

        $request->setApproved(ApprovedType::approved());
        $user->setDormId($request->getDormitory()->getId());
        $user->setRoomNr($request->getRoomNr());

        $this->entityManager->flush();
    }

    public function removeDormitoryChangeRequest(int $id): void
    {
        $requestRepo = $this->getRepository(DormitoryChange::class);
        $request = $requestRepo->find($id);

        if (!$request) {
            throw new \Exception('Request not found.');
        }

        $this->entityManager->remove($request);
        $this->entityManager->flush();
    }

    public function approveRoomChangeRequest(int $id): void
    {
        $requestRepo = $this->getRepository(RoomChange::class);
        $request = $requestRepo->find($id);

        if (!$request) {
            throw new \Exception('Request not found.');
        }

        $user = $request->getUser();
        $request->setApproved(ApprovedType::approved());
        $user->setRoomNr($request->getNewRoomNr());

        $this->entityManager->flush();
    }

    public function removeRoomChangeRequest(int $id): void
    {
        $requestRepo = $this->getRepository(RoomChange::class);
        $request = $requestRepo->find($id);

        if (!$request) {
            throw new \Exception('Request not found.');
        }

        $this->entityManager->remove($request);
        $this->entityManager->flush();
    }

    public function getReportedMessages(): array
    {
        $messagesRepo = $this->getRepository(Message::class);
        return $messagesRepo->getReportedMessages();
    }

    public function closeReport(int $id): void
    {
        $messagesRepo = $this->getRepository(Message::class);
        $message = $messagesRepo->find($id);

        if (!$message) {
            throw new \Exception('Message not found.');
        }

        $message->setReported(false);
        $this->entityManager->flush();
    }

    public function acceptReport(int $id): void
    {
        $messagesRepo = $this->getRepository(Message::class);
        $message = $messagesRepo->find($id);

        if (!$message) {
            throw new \Exception('Message not found.');
        }

        $this->entityManager->remove($message);
        $this->entityManager->flush();
    }

    public function studentStatus(int $id, $user): array
    {
        $studentsRepository = $this->getRepository(User::class);
        $student = $studentsRepository->findOneBy(['id' => $id]);
        $dormitoryRepository = $this->getRepository(Dormitory::class);
        $dorms = $dormitoryRepository->getUserDormitories($user->getId());

        foreach ($dorms as $dorm) {
            $dorm_ids[] = $dorm->getId();
        }

        if (!in_array($student->getDormId(), $dorm_ids)) {
            throw new \Exception('Student is not in dormitory.');
        }

        if ($student->getIsDisabled()===true) {
            $student->setIsDisabled(false);
            $blockStatus = 'unblock';
            $message = 'Paskyra atblokuota';
        } else {
            $student->setIsDisabled(true);
            $blockStatus = 'block';
            $message = 'Paskyra užblokuota';
        }

        $this->entityManager->persist($student);
        $this->entityManager->flush();
        return array('blockStatus' => $blockStatus, 'message' => $message);
    }
}
