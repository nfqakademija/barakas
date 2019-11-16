<?php

namespace App\Repository;

use App\Entity\Help;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Help|null find($id, $lockMode = null, $lockVersion = null)
 * @method Help|null findOneBy(array $criteria, array $orderBy = null)
 * @method Help[]    findAll()
 * @method Help[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Help::class);
    }

    public function findUserProvidedHelp($requester_id, $user_id)
    {
        $help = $this->findBy([
            'requester_id' => $requester_id,
            'user_id' => $user_id
        ]);

        return $help;
    }
}
