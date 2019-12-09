<?php


namespace App\Service;

use App\Entity\Academy;
use App\Entity\AcademyType;
use App\Entity\Dormitory;
use App\Entity\User;

class UserService extends Service
{

    public function insertDormitory(Dormitory $dormitory)
    {
        $dormitory->setAddress($dormitory->getAddress());
        $dormitory->setOrganisationId($this->getUser()->getId());
        $dormitory->setTitle($dormitory->getTitle());

        $this->entityManager->persist($dormitory);
        $this->entityManager->flush();
    }

    public function getUserDormitories()
    {
        $dormitoryRepository = $this->getRepository(Dormitory::class);
        return $dormitoryRepository->getUserDormitories($this->getUser()->getId());
    }

    public function getUniversities()
    {
        $academyRepository = $this->getRepository(Academy::class);
        return $academyRepository->findBy(['academyType' => AcademyType::university()->id()]);
    }

    public function getColleges()
    {
        $academyRepository = $this->getRepository(Academy::class);
        $academyRepository->findBy(['academyType' => AcademyType::college()->id()]);
    }

    public function insertOrganisation(User $organisation)
    {
        $plainPassword = $organisation->generateRandomPassword();
        $encodedPassword = $this->encoder->encodePassword($organisation, $plainPassword);
        $organisation->setPassword($encodedPassword);
        $organisation->setRoles(array('ROLE_ADMIN'));
        $organisation->setPoints(0);

        $this->entityManager->persist($organisation);
        $this->entityManager->flush();

        $this->emailService->sendOrganisationSignupMail($organisation->getEmail(), $plainPassword);
    }
}
