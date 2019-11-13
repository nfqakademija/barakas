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
     * @Route("/organisation/admin", name="Admin panel")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param EmailService $emailService
     * @return RedirectResponse|Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, EmailService $emailService)
    {
        
        
        $dormitoryRepository = $this->getDoctrine()->getRepository(Dormitory::class);
        $dormitories = $dormitoryRepository->findAll();

        $invitesRepository = $this->getDoctrine()->getRepository(Invite::class);
        $invites = $invitesRepository->findAll();

        $id = $request->query->get('id');
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
            $data = $form->getData();
            $url = $invitation->generateUrl();
            $invitation->setName($invitation->getName());
            $invitation->setEmail($invitation->getEmail());
            $invitation->setRoom($invitation->getRoom());
            $invitation->setUrl($url);
            $invitation->setDorm($dormitoryInfo->getId());
            $entityManager->persist($invitation);
            $entityManager->flush();
            $emailService->sendInviteMail($invitation->getEmail(), $url, $invitation->getName());
           // return $this->redirect('/organisation/admin?id={{ dormitory.id }}');
        }
        
        return $this->render('admin/index.html.twig', [
            'dormitories' => $dormitories,
            'dormitoryInfo' => $dormitoryInfo,
            'invites' => $invites,
            'dormitory' => $dormitory,
            'SendInvitationType' => $form->createView()
        ]);
    }
}
