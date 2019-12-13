<?php

namespace App\Controller;

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
use App\Service\UserService;
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
     * @param Request $request
     * @param UserService $userService
     * @return Response
     */
    public function addDormitory(Request $request, UserService $userService)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $dormitory = new Dormitory();
        $form = $this->createForm(DormAddFormType::class, $dormitory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userService->insertDormitory($dormitory);
            return $this->redirectToRoute('organisation');
        }
        return $this->render('organisation/pages/addDormitory.html.twig', [
            'DormAddFormType' => $form->createView(),
        ]);
    }

    /**
     * @Route("/organisation", name="organisation")
     * @param UserService $userService
     * @return Response
     */
    public function index(UserService $userService)
    {
        $dormitories = $userService->getUserDormitories();

        return $this->render('organisation/pages/organisation.html.twig', [
            'dormitories' => $dormitories
        ]);
    }

    /**
     * @Route("/registration", name="org_registration")
     * @param Request $request
     * @param UserService $userService
     * @return Response
     */
    public function register(
        Request $request,
        UserService $userService
    ) {
        
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $organisation = new User();
        $universities = $userService->getUniversities();
        $colleges = $userService->getColleges();

        $form = $this->createForm(UserRegisterType::class, $organisation, array(
            'universities' => $universities,
            'colleges' => $colleges
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userService->insertOrganisation($organisation);

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
     * @param UserService $userService
     * @return Response
     */
    public function passwordChange(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        UserInterface $user,
        UserService $userService
    ): Response {

        $form = $this->createForm(PasswordChangeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($encoder->isPasswordValid($user, $data['oldPassword']) && $data['password']===$data['newPassword']) {
                $userService->changePassword($data, $user);
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
     * @param UserService $userService
     * @return Response
     */
    public function generateStudentAccount(
        Request $request,
        UserService $userService
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
            $userService->insertStudentAccount($student, $request->get('invite'));
            $this->addFlash(
                'success',
                'Sveikiname sėkmingai užsiregistravus! Dabar galite prisijungti.'
            );
            return $this->redirectToRoute('home');
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("clear-notifications", name="clear_notifications")
     * @param UserService $userService
     * @return RedirectResponse
     */
    public function clearNotifications(
        UserService $userService
    ) {
        $notifications = $userService->getNotificationsByUser();
        $userService->deleteAll($notifications);
        return $this->redirectToRoute('dormitory', ['id' => $this->getUser()->getDormId()]);
    }

    /**
     * @Route("/dormitory/help-provided", name="provided_help")
     * @param UserService $userService
     * @return Response
     */
    public function providedHelp(
        UserService $userService
    ) {
        $helpMessages = $userService->getHelpMessages();

        return $this->render('user/messages_solved.html.twig', [
            'helpMessages' => $helpMessages,
        ]);
    }

    /**
     * @Route("/dormitory/change-dormitory", name="change_dormitory")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserService $userService
     * @return Response
     */
    public function changeDormitory(Request $request, EntityManagerInterface $entityManager, UserService $userService)
    {
        $user = $this->getUser();
        $changeDormitory = new DormitoryChange();
        $dormitoryChangeRepo = $this->getDoctrine()->getRepository(DormitoryChange::class);
        $dorms = $dormitoryChangeRepo->removeUserDormitoryFromArray($user, $user->getDormId());

        $form = $this->createForm(DormitoryChangeType::class, $changeDormitory, array(
            'dorms' => $dorms
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userService->insertChangeDormitory($changeDormitory);

            $this->addFlash('success', 'Prašymas buvo sėkmingai išsiųstas, 
            kuris bus peržiūrėtas per 24 val.');

            return $this->redirectToRoute('dormitory', ['id' => $this->getUser()->getDormId()]);
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
    public function addRules(Request $request, EntityManagerInterface $entityManager, UserService $userService)
    {
        $dorm_id = $request->get('id');
        $formRequest = $this->createForm(AddRulesType::class);
        $formRequest->handleRequest($request);
        $dorm = $userService->getOrganisationDormitoryById($dorm_id);
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
     * @param UserService $userService
     * @return Response
     */
    public function changeRoom(Request $request, UserService $userService)
    {
        $user = $this->getUser();
        $roomChange = new RoomChange();

        $form = $this->createForm(RoomChangeType::class, $roomChange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notApprovedRequest = $userService->findNotApprovedUserRoomChange();
            if ($notApprovedRequest) {
                $this->addFlash('danger', 'Jūs jau esate išsiuntęs prašymą dėl kambario keitimo.');
                return $this->redirectToRoute('change_room');
            }
            if ($user->getRoomNr() === $roomChange->getNewRoomNr()) {
                $this->addFlash('danger', 'Jūs jau esate nurodytame kambaryje.');
                return $this->redirectToRoute('change_room');
            }

            $userService->insertChangeRoom($roomChange);

            $this->addFlash('success', 'Prašymas buvo sėkmingai išsiųstas, 
            kuris bus peržiūrėtas per 24 val.');

            return $this->redirectToRoute('dormitory', ['id' => $this->getUser()->getDormId()]);
        }

        return $this->render('user/change_room.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/my-messages", name="my-messages")
     * @param UserService $userService
     * @return RedirectResponse|Response
     */
    public function userMessages(UserService $userService)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('organisation');
        }
        $messages = $userService->getUserMessages();

        return $this->render('user/messages.html.twig', [
            'messages' => $messages
        ]);
    }
}
