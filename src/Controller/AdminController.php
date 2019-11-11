<?php

namespace App\Controller;

use App\Entity\Dormitory;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/organisation/admin", name="Admin panel")
     */
    public function index(Request $request)
    {
		$repository = $this->getDoctrine()->getRepository(Dormitory::class);
		$userRepository = $this->getDoctrine()->getRepository(User::class);

        $dormitories = $repository->findAll();
		$id = $request->query->get('id');
		$dormitoryInfo = $this->getDoctrine()->getRepository(Dormitory::class)->find($id);
		$organisationID = $dormitoryInfo->getOrganisationId();
		$dormitory = $userRepository->findOneBy(
		['id' => $organisationID]
		);
		
		
		
		if(!$dormitory) {
		return $this->redirectToRoute('home');
		}
		
		
        return $this->render('admin/index.html.twig', [
            'dormitories' => $dormitories,
            'dormitoryInfo' => $dormitoryInfo,
            'dormitory' => $dormitory
        ]);
    }
}
