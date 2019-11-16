<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getStudents($id)
    {
        $entityManager = $this->getEntityManager();
        $repo = $entityManager->getRepository(User::class);

        $invites = $repo->findBy(['dorm_id' => $id]);

        return $invites;
    }

    public function getUserMessages($id)
    {
        $entityManager = $this->getEntityManager();
        $messagesRepo = $entityManager->getRepository(Message::class);

        $messages = $messagesRepo->findBy([
            'user_id' => $id
        ]);

        return $messages;
    }
}
