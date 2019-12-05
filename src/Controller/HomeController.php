<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        if ($this->getUser()) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('organisation');
            } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER')) {
                return $this->redirectToRoute('easyadmin');
            }
            return $this->redirectToRoute('dormitory');
        }
        return $this->render('home/index.html.twig');
    }
    
    /**
     * @Route("/contacts", name="contacts")
     */
    public function contacts()
    {
        return $this->render('home/contacts.html.twig');
    }
}
