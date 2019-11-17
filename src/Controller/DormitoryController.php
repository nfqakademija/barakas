<?php

namespace App\Controller;

use App\Entity\Dormitory;
use App\Entity\Help;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\SolvedType;
use App\Entity\StatusType;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DormitoryController extends AbstractController
{
    /**
     * @Route("/dormitory", name="dormitory")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function index(
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $user = $this->getUser();

        $dormitoryRepo = $this->getDoctrine()->getRepository(Dormitory::class);
        $notificationRepo = $this->getDoctrine()->getRepository(Notification::class);
        $helpRepo = $this->getDoctrine()->getRepository(Help::class);

        $dormitory = $dormitoryRepo->getLoggedInUserDormitory($user->getDormId());
        $students = $dormitoryRepo->getStudentsInDormitory($user->getDormId());
        $messages = $dormitoryRepo->getDormitoryMessages($user->getDormId());
        $notifications = $notificationRepo->getNotificationsByUser($user->getId());
        $helpMessages = $helpRepo->userProblemSolvers($user->getId());

        $message = new Message();
        $formRequest = $this->createForm(MessageType::class, $message);

        $formRequest->handleRequest($request);

        if ($formRequest->isSubmitted() && $formRequest->isValid()) {
            $message->setUser($user->getOwner());
            $message->setUserId($user->getId());
            $message->setDormId($user->getDormId());
            $message->setRoomNr($user->getRoomNr());
            $message->setContent($message->getContent());
            $message->setStatus(StatusType::urgent()->id());
            $message->setSolved(SolvedType::notSolved()->id());
            $message->setCreatedAt(new \DateTime());

            $entityManager->persist($message);
            $entityManager->flush();

            $studentToRemove = null;

            foreach ($students as $struct) {
                if ($user->getOwner() == $struct->getOwner()) {
                    $studentToRemove = $struct;
                    break;
                }
            }

            $key = array_search($studentToRemove, $students);
            unset($students[$key]);

            foreach ($students as $student) {
                $notification = new Notification();
                $notification->setUser($message->getUser());
                $notification->setCreatedAt(new \DateTime());
                $notification->setRoomNr($message->getRoomNr());
                $notification->setDormId($message->getDormId());
                $notification->setContent($message->getContent());
                $notification->setRecipientId($student->getId());
                $notification->setMessageId($message->getId());
                $entityManager->persist($notification);
                $entityManager->flush();
            }

            $this->addFlash('success', 'Prašymas išsiųstas sėkmingai!');
            return $this->redirectToRoute('dormitory');
        }

        if ($formRequest->isSubmitted() && !$formRequest->isValid()) {
            $this->addFlash('error', 'Prašymas nebuvo išsiųstas. Prašymas turi sudaryti nuo 7 iki 300 simbolių.');
            return $this->redirectToRoute('dormitory');
        }


        return $this->render('dormitory/index.html.twig', [
            'dormitory' => $dormitory,
            'students' => $students,
            'messages' => $messages,
            'notifications' => $notifications,
            'helpMessages' => $helpMessages,
            'formRequest' => $formRequest->createView(),
        ]);
    }

    /**
     * @Route("dormitory/message/{id}", name="message")
     * @param $id
     * @return Response
     */
    public function showMessage($id)
    {
        $user = $this->getUser();
        $messagesRepo = $this->getDoctrine()->getRepository(Message::class);
        $notificationRepo = $this->getDoctrine()->getRepository(Notification::class);
        $dormitoryRepo = $this->getDoctrine()->getRepository(Dormitory::class);
        $helpRepo = $this->getDoctrine()->getRepository(Help::class);

        $message = $messagesRepo->find($id);
        $notifications = $notificationRepo->getNotificationsByUser($user->getId());
        $dormitory = $dormitoryRepo->getLoggedInUserDormitory($user->getDormId());
        $students = $dormitoryRepo->getStudentsInDormitory($user->getDormId());
        $helpMessages = $helpRepo->userProblemSolvers($user->getId());

        if (!$message) {
            return $this->redirectToRoute('dormitory');
        }

        return $this->render('dormitory/message.html.twig', [
            'message' => $message,
            'notifications' => $notifications,
            'dormitory' => $dormitory,
            'helpMessages' => $helpMessages,
            'students' => $students
        ]);
    }

    /**
     * @Route("/dormitory/help/{id}", name="dormitory_help")
     */
    public function helpUser($id, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $messagesRepo = $this->getDoctrine()->getRepository(Message::class);
        $dormitoryRepo = $this->getDoctrine()->getRepository(Dormitory::class);
        $helpRepo = $this->getDoctrine()->getRepository(Help::class);

        $message = $messagesRepo->find($id);
        $dormitory = $dormitoryRepo->getLoggedInUserDormitory($user->getDormId());
        $help = $helpRepo->findUserProvidedHelp($message->getUserId(), $user->getId(), $message->getId());

        if (!$message || $help) {
            return $this->redirectToRoute('dormitory');
        }

        $help = new Help();
        $help->setMessageId($id);
        $help->setUserId($user->getId());
        $help->setDormId($dormitory->getId());
        $help->setRoomNr($user->getRoomNr());
        $help->setRequesterId($message->getUserId());
        $help->setCreatedAt(new \DateTime());

        $entityManager->persist($help);
        $message->setSolved(SolvedType::solved()->id());

        $entityManager->flush();


        $this->addFlash('success', 'Pagalbos siūlymas išsiųstas sėkmingai!');

        return $this->redirectToRoute('dormitory');
    }
}
