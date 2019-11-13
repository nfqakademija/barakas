<?php

namespace App\Controller;

use App\Entity\Dormitory;
use App\Entity\User;
use App\Form\SendInvitationType;
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
     * @return RedirectResponse|Response
     */
    public function index(Request $request)
    {
        
        
        $dormitoryRepository = $this->getDoctrine()->getRepository(Dormitory::class);
        $dormitories = $dormitoryRepository->findAll();

        $id = $request->query->get('id');
        $dormitoryInfo = $dormitoryRepository->find($id);

        $organisationID = $dormitoryInfo->getOrganisationId();

        $dormitory = $dormitoryRepository->findDormitory($organisationID);

        if (!$dormitory) {
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(SendInvitationType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            dump($data);
        }
        return $this->render('admin/index.html.twig', [
            'dormitories' => $dormitories,
            'dormitoryInfo' => $dormitoryInfo,
            'dormitory' => $dormitory,
            'SendInvitationType' => $form->createView()
        ]);
    }
}
