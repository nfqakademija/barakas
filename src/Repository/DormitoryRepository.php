<?php

namespace App\Repository;

use App\Entity\Dormitory;
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

    public function findDormitory($id)
    {
        $entityManager = $this->getEntityManager();
        $repo = $entityManager->getRepository(User::class);

        $dormitory = $repo->findOneBy(
            ['id' => $id]
        );
        return $dormitory;
    }
}
