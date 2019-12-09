<?php

namespace App\Controller;

use App\Entity\Invite;
use App\Form\SendInvitationType;
use App\Service\AdminService;
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
     * @param AdminService $adminService
     * @return RedirectResponse|Response
     */
    public function index(Request $request, AdminService $adminService)
    {
        $id = $request->query->get('id');
        $adminPageInfo = $adminService->indexPage($id);

        if (!$adminPageInfo) {
            return $this->redirectToRoute('home');
        }

        $organisationDormitory = $adminService->getOrganisationDormitory($id);

        $invitation = new Invite();
        $form = $this->createForm(SendInvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dormitory = $adminService->getDormitory($id);
            $sendInvitation = $adminService->addNewStudentToDormitory($form->getData(), $invitation, $dormitory);

            if (!$sendInvitation) {
                $this->addFlash('warning', 'El. pašto adresas jau užregistruotas.');
                return $this->redirectToRoute('admin_panel', ['id' => $id]);
            }

            $this->addFlash('success', 'Pakvietimas studentui sėkmingai išsiųstas.');

            return $this->redirectToRoute('admin_panel', ['id' => $id]);
        }

        return $this->render('admin/index.html.twig', [
            'dormitoryInfo' => $adminPageInfo['dormitoryInfo'],
            'invites' => $adminPageInfo['invites'],
            'students' => $adminPageInfo['students'],
            'dormitory' => $organisationDormitory,
            'SendInvitationType' => $form->createView()
        ]);
    }

    /**
     * @Route("organisation/dormitory-change-requests", name="dormitory_change_req")
     * @param EntityManagerInterface $entityManager
     * @param AdminService $adminService
     * @return Response
     */
    public function dormitoryChangeRequests(EntityManagerInterface $entityManager, AdminService $adminService)
    {
        $requests = $adminService->getDormitoryChangeRequests();
        return $this->render('/organisation/pages/dormitoryChangeRequests.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/organisation/approve-dormitory-change-request", name="approve_change_dorm_req")
     * @param Request $request
     * @param AdminService $adminService
     * @return Response
     */
    public function approveDormitoryChangeRequest(Request $request, AdminService $adminService)
    {
        $requestId = $request->query->get('id');
        $request = $adminService->approveDormitoryChangeRequest($requestId);
        
        if (!$request) {
            return $this->redirectToRoute('dormitory_change_req');
        }

        $this->addFlash('success', 'Prašymas patvirtintas sėkmingai.');
        return $this->redirectToRoute('dormitory_change_req');
    }

    /**
     * @Route("/organisation/remove-dormitory-change-request", name="remove_change_dorm_req")
     * @param Request $request
     * @param AdminService $adminService
     * @return RedirectResponse
     */
    public function removeDormitoryChangeRequest(Request $request, AdminService $adminService)
    {
        $requestId = $request->query->get('id');
        $request = $adminService->removeDormitoryChangeRequest($requestId);

        if (!$request) {
            return $this->redirectToRoute('dormitory_change_req');
        }

        $this->addFlash('success', 'Prašymas ištrintas sėkmingai.');
        return $this->redirectToRoute('dormitory_change_req');
    }

    /**
     * @Route("organisation/room-change-requests", name="room_change_req")
     * @param AdminService $adminService
     * @return Response
     */
    public function roomChangeRequests(AdminService $adminService)
    {
        $requests = $adminService->getRoomChangeRequests();

        return $this->render('/organisation/pages/roomChangeRequests.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/organisation/approve-room-change-request", name="approve_change_room_req")
     * @param Request $request
     * @param AdminService $adminService
     * @return Response
     */
    public function approveRoomChangeRequest(Request $request, AdminService $adminService)
    {
        $requestId = $request->query->get('id');
        $request = $adminService->approveRoomChangeRequest($requestId);

        if (!$request) {
            $this->redirectToRoute('room_change_req');
        }

        $this->addFlash('success', 'Prašymas patvirtintas sėkmingai.');
        return $this->redirectToRoute('room_change_req');
    }

    /**
     * @Route("/organisation/remove-room-change-request", name="remove_change_room_req")
     * @param Request $request
     * @param AdminService $adminService
     * @return RedirectResponse
     */
    public function removeRoomChangeRequest(Request $request, AdminService $adminService)
    {
        $requestId = $request->query->get('id');
        $request = $adminService->removeRoomChangeRequest($requestId);

        if (!$request) {
            $this->redirectToRoute('room_change_req');
        }

        $this->addFlash('success', 'Prašymas ištrintas sėkmingai.');
        return $this->redirectToRoute('room_change_req');
    }

    /**
     * @Route("/organisation/accountdisable", name="disable_account")
     * @param Request $request
     * @param AdminService $adminService
     * @param UserInterface $user
     * @return RedirectResponse
     */
    public function toggleAccount(Request $request, AdminService $adminService, UserInterface $user)
    {
        $changeStudentStatus = $adminService->studentStatus($request->get('id'), $user);

        if (!$changeStudentStatus) {
            return $this->redirect($request->headers->get('referer'));
        }

        $this->addFlash('success', $changeStudentStatus['message']);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/organisation/reported-messages", name="reportedMessages")
     * @param AdminService $adminService
     * @return Response
     */
    public function reportedMessages(AdminService $adminService)
    {
        $messages = $adminService->getReportedMessages();
        return $this->render('/organisation/pages/reportedMessages.html.twig', [
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/organisation/close-report", name="closeReport")
     * @param Request $request
     * @param AdminService $adminService
     * @return RedirectResponse
     */
    public function closeReport(Request $request, AdminService $adminService)
    {
        $messageId = $request->get('id');
        $request = $adminService->closeReport($messageId);

        if (!$request) {
            return $this->redirectToRoute('reportedMessages');
        }

        $this->addFlash('success', 'Įspėjimas apie blogą pranešimą pašalintas.');
        return $this->redirectToRoute('reportedMessages');
    }

    /**
     * @Route("/organisation/accept-report", name="acceptReport")
     * @param Request $request
     * @param AdminService $adminService
     * @return RedirectResponse
     */
    public function acceptReport(Request $request, AdminService $adminService)
    {
        $messageId = $request->get('id');
        $request = $adminService->acceptReport($messageId);

        if (!$request) {
            return $this->redirectToRoute('reportedMessages');
        }

        $this->addFlash('success', 'Pranešimas buvo sėkmingai pašalintas.');
        return $this->redirectToRoute('reportedMessages');
    }
}
