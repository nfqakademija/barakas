<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Form\OrganisationRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrganisationController extends AbstractController
{
    /**
     * @Route("/organizacijos-registracija", name="organisation-registration")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $entityManager)
    {
        $organisation = new Organisation();

        $form = $this->createForm(OrganisationRegisterType::class, $organisation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($organisation);
            $entityManager->flush();
        }

        return $this->render('organisation/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
