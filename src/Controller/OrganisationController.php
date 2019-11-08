<?php

namespace App\Controller;

use App\Entity\EmailSend;
use App\Entity\Organisation;
use App\Form\OrganisationRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class OrganisationController extends AbstractController
{
    /**
     * @Route("/organizacijos-registracija", name="organisation-registration")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        MailerInterface $mailer
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
                $organisation->getAcademy(),
                $encodedPassword
            );

            $entityManager->persist($organisation);
            $entityManager->flush();

            $email = (new Email())
                ->from($this->getParameter('serverEmail'))
                ->to($organisation->getEmail())
                ->subject(EmailSend::getSubject())
                ->html(EmailSend::signupOrganisationEmail($organisation->getEmail(), $plainPassword));

            $mailer->send($email);


            return $this->render('organisation/register/success.html.twig', [
                'email' => $organisation->getEmail(),
                'academy' => $organisation->getAcademy()->getTitle()
            ]);
        }

        return $this->render('organisation/register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
