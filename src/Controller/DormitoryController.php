<?php

namespace App\Controller;

use App\Entity\Dormitory;
use App\Entity\Message;
use App\Form\MessageType;
use App\Service\DormitoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DormitoryController extends AbstractController
{
    /**
     * @Route("/dormitory", name="dormitory")
     * @param Request $request
     * @param DormitoryService $dormitoryService
     * @param MessageBusInterface $bus
     * @return Response
     */
    public function index(Request $request, DormitoryService $dormitoryService)
    {
        $dormitoryInfo = $dormitoryService->getDormitoryInfo();

        if (!$dormitoryInfo) {
            return $this->redirectToRoute('home');
        }

        $message = new Message();
        $formRequest = $this->createForm(MessageType::class, $message);
        $formRequest->handleRequest($request);

        if ($formRequest->isSubmitted() && $formRequest->isValid()) {
//            if (!$dormitoryService->canSendMessage()) {
//                $this->addFlash('error', 'Jūs ką tik siuntėte pranešimą, bandykite vėl po 2 minučių.');
//                return $this->redirectToRoute('dormitory');
//            }

            $submitedMessage = $dormitoryService->postNewMessage($formRequest->getData());

            if (!$submitedMessage) {
                return $this->redirectToRoute('dormitory');
            }


            $this->addFlash('success', 'Prašymas išsiųstas sėkmingai!');
            return $this->redirectToRoute('dormitory');
        }

        if ($formRequest->isSubmitted() && !$formRequest->isValid()) {
            $this->addFlash('error', 'Prašymas nebuvo išsiųstas. Prašymas turi sudaryti nuo 7 iki 300 simbolių.');
            return $this->redirectToRoute('dormitory');
        }

        $loggedInUsers = $dormitoryService->getAllLoggedInUsers();

        return $this->render('dormitory/index.html.twig', [
            'dormitory' => $dormitoryInfo['dormitory'],
            'students' => $dormitoryInfo['students'],
            'messages' => $dormitoryInfo['messages'],
            'formRequest' => $formRequest->createView(),
            'loggedInUsers' => $loggedInUsers,
        ]);
    }

    /**
     * @Route("dormitory/message/{id}", name="message")
     * @param $id
     * @param DormitoryService $dormitoryService
     * @return Response
     */
    public function showMessage($id, DormitoryService $dormitoryService)
    {
        $user = $this->getUser();

        $message = $dormitoryService->findMessage($id);

        $dormitory = $dormitoryService->getLoggedInUserDormitory();
        $students = $dormitoryService->getStudentsInDormitory();

        if (!$message || $user->getDormId() !== $message->getDormId()) {
            return $this->redirectToRoute('dormitory');
        }

        return $this->render('dormitory/message.html.twig', [
            'message' => $message,
            'dormitory' => $dormitory,
            'students' => $students
        ]);
    }

    /**
     * @Route("/dormitory/help/{id}", name="dormitory_help")
     * @param $id
     * @param DormitoryService $dormitoryService
     * @return RedirectResponse
     */
    public function helpUser($id, DormitoryService $dormitoryService)
    {
        $help = $dormitoryService->provideHelp($id);

        if (!$help) {
            return $this->redirectToRoute('dormitory');
        }

        $this->addFlash('success', 'Pagalbos siūlymas išsiųstas sėkmingai!');
        return $this->redirectToRoute('dormitory');
    }

    /**
     * @Route("dormitory/rules", name="rules")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function rules(EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $dorm_id = $user->getDormId();
        $dorm = $entityManager->getRepository(Dormitory::class)->find($dorm_id);
        return $this->render('dormitory/rules.html.twig', [
            'rules' => $dorm->getRules()
        ]);
    }

    /**
     * @Route("/dormitory/report/message", name="reportMessage")
     * @param Request $request
     * @param DormitoryService $dormitoryService
     * @return RedirectResponse
     */
    public function reportMessage(Request $request, DormitoryService $dormitoryService)
    {
        $reportMessageId = $request->get('id');
        $reported = $dormitoryService->reportMessage($reportMessageId);

        if (!$reported) {
            return $this->redirectToRoute('dormitory');
        }

        $this->addFlash('success', 'Apie netinkamą pranešimą pranešta administracijai.');
        return $this->redirectToRoute('dormitory');
    }

    /**
     * @Route("/dormitory/accept-help-request", name="acceptHelp")
     * @param Request $request
     * @param DormitoryService $dormitoryService
     * @return RedirectResponse
     */
    public function acceptHelpRequest(Request $request, DormitoryService $dormitoryService)
    {
        $helpId = $request->get('id');
        $messageId = $request->get('msg');
        $acceptedHelp = $dormitoryService->acceptHelpRequest($helpId, $messageId);

        if ($acceptedHelp) {
            $this->addFlash('success', 'Pagalbos siūlymas patvirtintas.');
            return $this->redirectToRoute('provided_help');
        }
    }

    /**
     * @Route("/dormitory/deny-help-request", name="denyHelp")
     * @param Request $request
     * @param DormitoryService $dormitoryService
     * @return RedirectResponse
     */
    public function denyHelpRequest(Request $request, DormitoryService $dormitoryService)
    {
        $helpId = $request->get('id');
        $denyHelp = $dormitoryService->denyHelpRequest($helpId);

        if (!$denyHelp) {
            return $this->redirectToRoute('dormitory');
        }

        $this->addFlash('success', 'Pagalbos siūlymas pašalintas, pranešimas gražintas į pradinę stadiją.');
        return $this->redirectToRoute('provided_help');
    }

    /**
     * @Route("/dormitory/students", name="dormitory_leaderboard")
     * @param Security $security
     * @param DormitoryService $dormitoryService
     * @return Response
     */
    public function allDormitoryStudents(Security $security, DormitoryService $dormitoryService)
    {
        $user = $security->getUser();
        $dormitory = $dormitoryService->getDormitoryWithStudents($user);

        if (!$dormitory) {
            return $this->redirectToRoute('dormitory');
        }

        return $this->render('/dormitory/dormitory_leaderboard.html.twig', [
            'students' => $dormitory['students'],
            'dormitory' => $dormitory['dormitory']
        ]);
    }
}
