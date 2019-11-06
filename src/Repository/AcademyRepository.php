<?php

namespace App\Repository;

use App\Entity\Academy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Academy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Academy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Academy[]    findAll()
 * @method Academy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcademyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Academy::class);
    }

    public function getUniversities()
    {
        return Academy::university();
    }
}
