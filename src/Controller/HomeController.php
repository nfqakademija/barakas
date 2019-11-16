<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $notifications = [];
        if ($user = $this->getUser()) {
            $notificationRepo = $this->getDoctrine()->getRepository(Notification::class);
            $notifications = $notificationRepo->getNotificationsByUser($user->getId());
        }
        $this->getUser();

        return $this->render('home/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * @Route("/contacts", name="contacts")
     */
    public function contacts()
    {
        $notifications = [];
        if ($user = $this->getUser()) {
            $notificationRepo = $this->getDoctrine()->getRepository(Notification::class);
            $notifications = $notificationRepo->getNotificationsByUser($user->getId());
        }
        $this->getUser();

        return $this->render('home/contacts.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    private function getNotifications()
    {
        $user = $this->getUser();
        $notificationRepo = $this->getDoctrine()->getRepository(Notification::class);
        $notifications = $notificationRepo->getNotificationsByUser($user->getId());

        return $notifications;
    }
}
