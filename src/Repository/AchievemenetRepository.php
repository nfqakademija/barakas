<?php

namespace App\Repository;

use App\Entity\Achievemenet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Achievemenet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achievemenet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achievemenet[]    findAll()
 * @method Achievemenet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchievemenetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achievemenet::class);
    }
}
