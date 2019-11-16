<?php

namespace App\Repository;

use App\Entity\Help;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getHelpers($id)
    {
        $entityManager = $this->getEntityManager();
        $helpRepo = $entityManager->getRepository(Help::class);

        $helpers = $helpRepo->findBy(
            ['message_id' => $id]
        );

        return $helpers;
    }
}
