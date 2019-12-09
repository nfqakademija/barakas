<?php

namespace App\Controller;

use App\Entity\Academy;
use App\Entity\AcademyType;
use App\Entity\ApprovedType;
use App\Entity\DormitoryChange;
use App\Entity\Help;
use App\Entity\Invite;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\RoomChange;
use App\Entity\User;
use App\Entity\Dormitory;
use App\Form\AddRulesType;
use App\Form\DormitoryChangeType;
use App\Form\PasswordChangeType;
use App\Form\RoomChangeType;
use App\Form\StudentRegisterType;
use App\Form\UserRegisterType;
use App\Form\DormAddFormType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @throws Exception
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
        $user = $this->getUser();
        $dormitoryRepository = $this->getDoctrine()->getRepository(Dormitory::class);
        $dormitories = $dormitoryRepository->getUserDormitories($user->getId());

        return $this->render('organisation/pages/organisation.html.twig', [
            'dormitories' => $dormitories
        ]);
    }

    /**
     * @Route("/registration", name="org_registration")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @param EmailService $emailService
     * @return Response
     * @throws Exception
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
            $organisation->setPoints(0);

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
            }
        }

        return $this->render('user/passwordChange.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/invite", name="invite")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function generateStudentAccount(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager
    ) {
        $invitation = $this
            ->getDoctrine()
            ->getRepository(Invite::class)
            ->findOneBy(array('url' => $request->get('invite')));

        if (!$invitation || $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $student = new User();

        $form = $this->createForm(StudentRegisterType::class, $student, [
            'owner' => $invitation->getName(),
            'email' => $invitation->getEmail()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $student->getPassword();
            $encodedPassword = $encoder->encodePassword($student, $plainPassword);
            $student->setOwner($invitation->getName());
            $student->setEmail($invitation->getEmail());
            $student->setPassword($encodedPassword);
            $student->setDormId($invitation->getDorm());
            $student->setRoomNr($invitation->getRoom());
            $student->setRoles(array('ROLE_USER'));
            $student->setPoints(0);
            $entityManager->persist($student);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Sveikiname sėkmingai užsiregistravus! Dabar galite prisijungti.'
            );

            $entityManager->remove($invitation);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("clear-notifications", name="clear_notifications")
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function clearNotifications(EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $notificationRepo = $this->getDoctrine()->getRepository(Notification::class);
        $notifications = $notificationRepo->getNotificationsByUser($user->getId());

        foreach ($notifications as $notification) {
            $entityManager->remove($notification);
        }

        $entityManager->flush();
        return $this->redirectToRoute('dormitory');
    }

    /**
     * @Route("/dormitory/help-provided", name="provided_help")
     */
    public function providedHelp()
    {
        $user = $this->getUser();
        $helpRepo = $this->getDoctrine()->getRepository(Help::class);

        $helpMessages = $helpRepo->userProblemSolvers($user->getId());

        return $this->render('user/messages_solved.html.twig', [
            'helpMessages' => $helpMessages,
        ]);
    }

    /**
     * @Route("/dormitory/change-dormitory", name="change_dormitory")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function changeDormitory(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        $changeDormitory = new DormitoryChange();
        $dormitoryChangeRepo = $this->getDoctrine()->getRepository(DormitoryChange::class);
        $userRepo = $this->getDoctrine()->getRepository(User::class);

        $userDormitoryId = $user->getDormId();

        $academy = $userRepo->findUserAcademy($userDormitoryId);

        $dorms = $dormitoryChangeRepo->removeUserDormitoryFromArray($user, $userDormitoryId);

        $form = $this->createForm(DormitoryChangeType::class, $changeDormitory, array(
            'dorms' => $dorms
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $changeDormitory->setAcademy($academy);
            $changeDormitory->setUser($user);
            $changeDormitory->setApproved(ApprovedType::notApproved());
            $entityManager->persist($changeDormitory);
            $entityManager->flush();

            $this->addFlash('success', 'Prašymas buvo sėkmingai išsiųstas, 
            kuris bus peržiūrėtas per 24 val.');

            return $this->redirectToRoute('dormitory');
        }

        return $this->render('user/change_dormitory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/organisation/addRules", name="add_Rules")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addRules(Request $request, EntityManagerInterface $entityManager)
    {
        $dorm_id = $request->get('id');
        $formRequest = $this->createForm(AddRulesType::class);
        $formRequest->handleRequest($request);
        $user = $this->getUser();
        $dorm = $entityManager->getRepository(Dormitory::class)->getOrganisationDormitoryById($user->getId(), $dorm_id);
        if ($formRequest->isSubmitted() && $formRequest->isValid()) {
            $dorm->setRules($formRequest->getData()->getRules());
            $entityManager->persist($dorm);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Taisyklės pakeistos!!!'
            );

            return $this->redirectToRoute('admin_panel', ['id' => $dorm_id]);
        }
        return $this->render('organisation/pages/addRules.html.twig', [
            'id' => $dorm_id,
            'form' => $formRequest->createView(),
            'rules' => $dorm->getRules()
        ]);
    }

    /**
     * @Route("/change-room", name="change_room")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function changeRoom(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $userDormitoryId = $user->getDormId();
        $academy = $userRepo->findUserAcademy($userDormitoryId);

        $roomChange = new RoomChange();


        $form = $this->createForm(RoomChangeType::class, $roomChange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roomChangeRepo = $this->getDoctrine()->getRepository(RoomChange::class);
            $notApprovedRequest = $roomChangeRepo->findNotApprovedUserRoomChange($user);

            if ($notApprovedRequest) {
                $this->addFlash('danger', 'Jūs jau esate išsiuntęs prašymą dėl kambario keitimo.');
                return $this->redirectToRoute('change_room');
            }

            if ($user->getRoomNr() === $roomChange->getNewRoomNr()) {
                $this->addFlash('danger', 'Jūs jau esate nurodytame kambaryje.');
                return $this->redirectToRoute('change_room');
            }

            $roomChange->setCurrentRoom($user->getRoomNr());
            $roomChange->setUser($user);
            $roomChange->setApproved(ApprovedType::notApproved());
            $roomChange->setAcademy($academy);
            $entityManager->persist($roomChange);
            $entityManager->flush();

            $this->addFlash('success', 'Prašymas buvo sėkmingai išsiųstas, 
            kuris bus peržiūrėtas per 24 val.');

            return $this->redirectToRoute('dormitory');
        }

        return $this->render('user/change_room.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/my-messages", name="my-messages")
     */
    public function userMessages()
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('home');
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('organisation');
        }

        $messagesRepo = $this->getDoctrine()->getRepository(Message::class);
        $messages = $messagesRepo->getUserMessages($user);

        return $this->render('user/messages.html.twig', [
            'messages' => $messages
        ]);
    }
}
