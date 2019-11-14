<?php

namespace App\Controller;

use App\Entity\Dormitory;
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

        $dormitory = $dormitoryRepository->findDormitory($organisationID);

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
            $entityManager->persist($invitation);
            $entityManager->flush();
            $emailService->sendInviteMail($invitation->getEmail(), $url, $invitation->getName());

            $this->addFlash('success', 'Pakvietimas stundentui sėkmingai išsiųstas.');

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
}
