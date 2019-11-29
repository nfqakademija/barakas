<?php

namespace App\Repository;

use App\Entity\ApprovedType;
use App\Entity\RoomChange;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RoomChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomChange[]    findAll()
 * @method RoomChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomChange::class);
    }

    public function findNotApprovedRequests($user)
    {
        $requests = $this->findBy(['approved' => false, 'academy' => $user->getAcademy()]);

        return $requests;
    }

    public function findNotApprovedUserRoomChange($user)
    {
        $requests = $this->findBy(['user' => $user, 'approved' => false]);

        return $requests;
    }
}
