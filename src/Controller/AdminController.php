<?php

namespace App\Controller;

use App\Entity\ApprovedType;
use App\Entity\Dormitory;
use App\Entity\DormitoryChange;
use App\Entity\Message;
use App\Entity\RoomChange;
use App\Entity\User;
use App\Entity\Invite;
use App\Form\SendInvitationType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/organisation/admin", name="admin_panel")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param EmailService $emailService
     * @return RedirectResponse|Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, EmailService $emailService)
    {

        $dormitoryRepository = $this->getDoctrine()->getRepository(Dormitory::class);
        $invitesRepository = $this->getDoctrine()->getRepository(Invite::class);
        $studentsRepository = $this->getDoctrine()->getRepository(User::class);

        $id = $request->query->get('id');
        
        $invites = $invitesRepository->getInvitations($id);
        $students = $studentsRepository->getStudents($id);

        $dormitoryInfo = $dormitoryRepository->find($id);

        $organisationID = $dormitoryInfo->getOrganisationId();

        $dormitory = $dormitoryRepository->findOrganisationDormitory($organisationID);

        if (!$dormitory) {
            return $this->redirectToRoute('home');
        }
        
        $invitation = new Invite();
        $form = $this->createForm(SendInvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = $invitation->generateUrl();
            $invitation->setName($invitation->getName());
            $invitation->setEmail($invitation->getEmail());
            $invitation->setRoom($invitation->getRoom());
            $invitation->setUrl($url);
            $invitation->setDorm($dormitoryInfo->getId());

            $studentExists = $studentsRepository->findBy(['email' => $invitation->getEmail()]);

            if ($studentExists) {
                $this->addFlash('warning', 'El. pašto adresas jau užregistruotas.');
                return $this->redirectToRoute('admin_panel', ['id' => $id]);
            }

            $entityManager->persist($invitation);
            $entityManager->flush();
            $emailService->sendInviteMail($invitation->getEmail(), $url, $invitation->getName());

            $this->addFlash('success', 'Pakvietimas studentui sėkmingai išsiųstas.');

            return $this->redirectToRoute('admin_panel', ['id' => $id]);
        }
        
        return $this->render('admin/index.html.twig', [
            'dormitoryInfo' => $dormitoryInfo,
            'invites' => $invites,
            'students' => $students,
            'dormitory' => $dormitory,
            'SendInvitationType' => $form->createView()
        ]);
    }

    /**
     * @Route("organisation/dormitory-change-requests", name="dormitory_change_req")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function dormitoryChangeRequests(EntityManagerInterface $entityManager)
    {
        $requestsRepo = $this->getDoctrine()->getRepository(DormitoryChange::class);
        $requests = $requestsRepo->getNotApprovedRequests($this->getUser());

        return $this->render('/organisation/pages/dormitoryChangeRequests.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/organisation/approve-dormitory-change-request", name="approve_change_dorm_req")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function approveDormitoryChangeRequest(Request $request, EntityManagerInterface $entityManager)
    {
        $requestId = $request->query->get('id');
        $requestRepo = $this->getDoctrine()->getRepository(DormitoryChange::class);

        $request = $requestRepo->find($requestId);

        if (!$request) {
            return $this->redirectToRoute('organisation');
        }

        $user = $request->getUser();

        $request->setApproved(ApprovedType::approved());
        $user->setDormId($request->getDormitory()->getId());
        $user->setRoomNr($request->getRoomNr());

        $entityManager->flush();

        $this->addFlash('success', 'Prašymas patvirtintas sėkmingai.');

        return $this->redirectToRoute('dormitory_change_req');
    }

    /**
     * @Route("/organisation/remove-dormitory-change-request", name="remove_change_dorm_req")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function removeDormitoryChangeRequest(Request $request, EntityManagerInterface $entityManager)
    {
        $requestId = $request->query->get('id');
        $requestRepo = $this->getDoctrine()->getRepository(DormitoryChange::class);

        $request = $requestRepo->find($requestId);

        if (!$request) {
            return $this->redirectToRoute('organisation');
        }

        $entityManager->remove($request);
        $entityManager->flush();

        $this->addFlash('success', 'Prašymas ištrintas sėkmingai.');

        return $this->redirectToRoute('dormitory_change_req');
    }

    /**
     * @Route("organisation/room-change-requests", name="room_change_req")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function roomChangeRequests(EntityManagerInterface $entityManager)
    {
        $requestsRepo = $this->getDoctrine()->getRepository(RoomChange::class);
        $requests = $requestsRepo->findNotApprovedRequests($this->getUser());

        return $this->render('/organisation/pages/roomChangeRequests.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/organisation/approve-room-change-request", name="approve_change_room_req")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function approveRoomChangeRequest(Request $request, EntityManagerInterface $entityManager)
    {
        $requestId = $request->query->get('id');
        $requestRepo = $this->getDoctrine()->getRepository(RoomChange::class);

        $request = $requestRepo->find($requestId);

        if (!$request) {
            return $this->redirectToRoute('organisation');
        }

        $user = $request->getUser();

        $request->setApproved(ApprovedType::approved());
        $user->setRoomNr($request->getNewRoomNr());

        $entityManager->flush();

        $this->addFlash('success', 'Prašymas patvirtintas sėkmingai.');

        return $this->redirectToRoute('room_change_req');
    }

    /**
     * @Route("/organisation/remove-room-change-request", name="remove_change_room_req")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function removeRoomChangeRequest(Request $request, EntityManagerInterface $entityManager)
    {
        $requestId = $request->query->get('id');
        $requestRepo = $this->getDoctrine()->getRepository(RoomChange::class);

        $request = $requestRepo->find($requestId);

        if (!$request) {
            return $this->redirectToRoute('organisation');
        }

        $entityManager->remove($request);
        $entityManager->flush();

        $this->addFlash('success', 'Prašymas ištrintas sėkmingai.');

        return $this->redirectToRoute('room_change_req');
    }

    /**
     * @Route("/organisation/accountdisable", name="disable_account")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserInterface $user
     * @return RedirectResponse
     */
    public function toggleAccount(Request $request, EntityManagerInterface $entityManager, UserInterface $user)
    {
            $user_id = $request->get('id');
            $studentsRepository = $this->getDoctrine()->getRepository(User::class);
            $student = $studentsRepository->findOneBy(['id' => $user_id]);
            $dormitoryRepository = $this->getDoctrine()->getRepository(Dormitory::class);
            $dorms = $dormitoryRepository->getUserDormitories($user->getId());

        foreach ($dorms as $dorm) {
            $dorm_ids[] = $dorm->getId();
        }

        if (!in_array($student->getDormId(), $dorm_ids)) {
            return $this->redirect($request->headers->get('referer'));
        }

        if ($student->getIsDisabled()===true) {
            $student->setIsDisabled(false);
            $this->addFlash('success', 'Paskyra atblokuota');
        } else {
            $student->setIsDisabled(true);
            $this->addFlash('success', 'Paskyra užblokuota');
        }
        $entityManager->persist($student);
        $entityManager->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/organisation/reported-messages", name="reportedMessages")
     */
    public function reportedMessages()
    {
        $messagesRepo = $this->getDoctrine()->getRepository(Message::class);
        $messages = $messagesRepo->getReportedMessages();

        return $this->render('/organisation/pages/reportedMessages.html.twig', [
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/organisation/close-report", name="closeReport")
     */
    public function closeReport(Request $request, EntityManagerInterface $entityManager)
    {
        $messageId = $request->get('id');
        $messagesRepo = $this->getDoctrine()->getRepository(Message::class);
        $message = $messagesRepo->find($messageId);

        if(!$message) {
            return $this->redirectToRoute('organisation');
        }

        $message->setReported(false);
        $entityManager->flush();

        $this->addFlash('success', 'Įspėjimas apie blogą pranešimą pašalintas.');

        return $this->redirectToRoute('reportedMessages');
    }

    /**
     * @Route("/organisation/accept-report", name="acceptReport")
     */
    public function acceptReport(Request $request, EntityManagerInterface $entityManager)
    {
        $messageId = $request->get('id');
        $messagesRepo = $this->getDoctrine()->getRepository(Message::class);
        $message = $messagesRepo->find($messageId);

        if(!$message) {
            return $this->redirectToRoute('organisation');
        }

        $entityManager->remove($message);
        $entityManager->flush();

        $this->addFlash('success', 'Pranešimas buvo sėkmingai pašalintas.');

        return $this->redirectToRoute('reportedMessages');
    }
}
