<?php

namespace App\Controller;

use App\Entity\Academy;
use App\Entity\AcademyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $entityManager)
    {
        $academy = new Academy();
        $academy->setTitle('Nauja profke');
        $academy->setAcademyType(AcademyType::technical());

        $entityManager->persist($academy);
        $entityManager->flush();

        dump($academy->getAcademyType());

        return $this->render('home/index.html.twig', [
            'someVariable' => 'NFQ Akademija test',
        ]);
    }
}
