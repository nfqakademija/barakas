<?php

namespace App\Controller;

use App\Entity\Dormitory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DormitoryController extends AbstractController
{
    /**
     * @Route("/dormitory", name="dormitory")
     */
    public function index()
    {
        $user = $this->getUser();
        $dormitoryRepo = $this->getDoctrine()->getRepository(Dormitory::class);
        $dormitory = $dormitoryRepo->getLoggedInUserDormitory($user->getDormId());
        $students = $dormitoryRepo->getStudentsInDormitory($user->getDormId());

        return $this->render('dormitory/index.html.twig', [
            'dormitory' => $dormitory,
            'students' => $students
        ]);
    }
}
