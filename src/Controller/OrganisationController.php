<?php

namespace App\Controller;

use App\Entity\College;
use App\Entity\Organisation;
use App\Entity\University;
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
            $encodedPassword = $encoder->encodePassword($organisation, $plainPassword);

            $organisation = Organisation::create(
                $organisation->getOwner(),
                $organisation->getEmail(),
                $organisation->getAcademyTitle(),
                $encodedPassword
            );

            $entityManager->persist($organisation);
            $entityManager->flush();

            return $this->render('organisation/register/success.html.twig', [
                'email' => $organisation->getEmail(),
                'academy' => $organisation->getAcademyTitle()
            ]);
        }

        return $this->render('organisation/register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
