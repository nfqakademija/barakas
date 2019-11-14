<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DormitoryController extends AbstractController
{
    /**
     * @Route("/dormitory", name="dormitory")
     */
    public function index()
    {
        return $this->render('dormitory/index.html.twig');
    }
}
