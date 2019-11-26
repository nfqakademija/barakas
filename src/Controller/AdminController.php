<?php

namespace App\Controller;

use App\Entity\ApprovedType;
use App\Entity\Dormitory;
use App\Entity\DormitoryChange;
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
        $requests = $requestsRepo->getNotApprovedRequests();

        return $this->render('/organisation/pages/dormitoryChangeRequests.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/organisation/approve-request", name="approve_change_dorm_req")
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
     * @Route("/organisation/remove-request", name="`remove_change_dorm_req`")
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
}
