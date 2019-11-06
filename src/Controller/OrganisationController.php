<?php

namespace App\Controller;

use App\Entity\Organisation;
use App\Form\OrganisationRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class OrganisationController extends AbstractController
{
    /**
     * @Route("/organizacijos-registracija", name="organisation-registration")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws Exception
     */
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $organisation = new Organisation();

        $form = $this->createForm(OrganisationRegisterType::class, $organisation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $organisation->generateRandomPassword();

            $encoded = $encoder->encodePassword($organisation, $plainPassword);
            $organisation->setPassword($encoded);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($organisation);
            $entityManager->flush();

            $email = $form->getData()->getEmail();
            $academy = $form->getData()->getAcademyTitle();

            return $this->render('organisation/register/success.html.twig', [
                'email' => $email,
                'academy' => $academy,
            ]);
        }

        return $this->render('organisation/register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
