<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OrganisationController extends AbstractController
{
    /**
     * @Route("/organizacijos-registracija", name="organisation-registration")
     */
    public function register()
    {
        return $this->render('organisation/register.html.twig', [
            'controller_name' => 'OrganisationController',
        ]);
    }
}
