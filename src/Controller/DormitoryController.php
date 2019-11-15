<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Dormitory;
use App\Entity\Message;
use App\Entity\StatusType;
use App\Form\CommentType;
use App\Form\MessageType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $dormitory = $dormitoryRepo->getLoggedInUserDormitory($user->getDormId());
        $students = $dormitoryRepo->getStudentsInDormitory($user->getDormId());
        $messages = $dormitoryRepo->getDormitoryMessages($user->getDormId());

        $message = new Message();
        $formRequest = $this->createForm(MessageType::class, $message);

        $formRequest->handleRequest($request);

        if ($formRequest->isSubmitted() && $formRequest->isValid()) {
            $message->setUser($user->getOwner());
            $message->setDormId($user->getDormId());
            $message->setRoomNr($user->getRoomNr());
            $message->setContent($message->getContent());
            $message->setStatus(StatusType::newRequest()->id());
            $message->setCreatedAt(new \DateTime());

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Prašymas išsiųstas sėkmingai!');
            return $this->redirectToRoute('dormitory');
        }


        return $this->render('dormitory/index.html.twig', [
            'dormitory' => $dormitory,
            'students' => $students,
            'messages' => $messages,
            'formRequest' => $formRequest->createView(),
        ]);
    }
}
