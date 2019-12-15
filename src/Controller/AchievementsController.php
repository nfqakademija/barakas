<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AchievementsController extends AbstractController
{
    /**
     * @Route("/achievements", name="achievements")
     */
    public function index()
    {
        return $this->render('achievements/index.html.twig', [
            'controller_name' => 'AchievementsController',
        ]);
    }
}
