<?php

namespace App\Controller;

use App\Entity\Academy;
use App\Entity\AcademyType;
use App\Entity\User;
use App\Entity\Dormitory;
use App\Form\PasswordChangeType;
use App\Form\UserRegisterType;
use App\Form\DormAddFormType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/organisation/add", name="addOrganisation")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    public function addDormitory(EntityManagerInterface $em, Request $request)
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $dormitory = new Dormitory();

        $form = $this->createForm(DormAddFormType::class, $dormitory);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dormitory->setAddress($dormitory->getAddress());
            $dormitory->setOrganisationId($user->getId());
            $dormitory->setTitle($dormitory->getTitle());

            $em->persist($dormitory);
            $em->flush();

            return $this->redirectToRoute('organisation');
        }
        return $this->render('organisation/pages/addDormitory.html.twig', [
            'DormAddFormType' => $form->createView(),
        ]);
    }
    /**
     * @Route("/organisation", name="organisation")
     */
    public function index()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $dormitoryRepository = $this->getDoctrine()->getRepository(Dormitory::class);
        $dormitories = $dormitoryRepository->getUserDormitories($user->getId());

        return $this->render('organisation/pages/organisation.html.twig', [
            'dormitories' => $dormitories
        ]);
    }
    /**
     * @Route("/organisation/invite", name="Invite Students", methods={"POST"})
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    public function generateStudentAccount(EntityManagerInterface $em, Request $request)
    {
        return new Response('Labas');
    }
    /**
     * @Route("/registration", name="org_registration")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @param EmailService $emailService
     * @return Response
     */
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        EmailService $emailService
    ) {
        
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $organisation = new User();

        $academyRepository = $this->getDoctrine()->getRepository(Academy::class);

        $universities = $academyRepository->findBy(['academyType' => AcademyType::university()->id()]);
        $colleges = $academyRepository->findBy(['academyType' => AcademyType::college()->id()]);


        $form = $this->createForm(UserRegisterType::class, $organisation, array(
            'universities' => $universities,
            'colleges' => $colleges
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $organisation->generateRandomPassword();
            $encodedPassword = $encoder->encodePassword($organisation, $plainPassword);
            $organisation->setPassword($encodedPassword);
            $organisation->setRoles(array('ROLE_ADMIN'));
            $entityManager->persist($organisation);
            $entityManager->flush();

            $emailService->sendOrganisationSignupMail($organisation->getEmail(), $plainPassword);

            return $this->render('organisation/register/success.html.twig', [
                'email' => $organisation->getEmail(),
                'academy' => $organisation->getAcademy()->getTitle()
            ]);
        }

        return $this->render('organisation/register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/changepassword", name="passwordChange")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @param UserInterface $user
     * @return Response
     */
    public function passwordChange(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager,
        UserInterface $user
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(PasswordChangeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($encoder->isPasswordValid($user, $data['oldPassword']) && $data['password']===$data['newPassword']) {
                $newPassword = $encoder->encodePassword($user, $data['password']);
                $user->setPassword($newPassword);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Slaptažodis pakeistas!'
                );
            } else {
                $form->addError(new FormError('Slaptažodis netinka'));
            }
        }

        return $this->render('user/passwordChange.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
