<?php

namespace App\Repository;

use App\Entity\Dormitory;
use App\Entity\Invite;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Dormitory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dormitory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dormitory[]    findAll()
 * @method Dormitory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DormitoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dormitory::class);
    }

    public function findOrganisationDormitory($id)
    {
        $entityManager = $this->getEntityManager();
        $repo = $entityManager->getRepository(User::class);

        $dormitory = $repo->findOneBy(
            ['id' => $id]
        );
        return $dormitory;
    }

    public function getUserDormitories($id)
    {
        $entityManager = $this->getEntityManager();
        $repo = $entityManager->getRepository(Dormitory::class);

        $dormitories = $repo->findBy(
            ['organisation_id' => $id]
        );

        return $dormitories;
    }

    public function getLoggedInUserDormitory($id)
    {
        $entityManager = $this->getEntityManager();
        $dormitoryRepo = $entityManager->getRepository(Dormitory::class);

        $dormitory = $dormitoryRepo->find($id);
        return $dormitory;
    }

    public function getStudentsInDormitory($id)
    {
        $entityManager = $this->getEntityManager();
        $studentsRepo = $entityManager->getRepository(User::class);

        $students = $studentsRepo->findBy(
            ['dorm_id' => $id]
        );

        return $students;
    }

    public function getDormitoryMessages($id)
    {
        $entityManager = $this->getEntityManager();
        $messagesRepo = $entityManager->getRepository(Message::class);

        $messages = $messagesRepo->findBy(
            ['dorm_id' => $id],
            ['created_at' => 'DESC']
        );

        return $messages;
    }
}
