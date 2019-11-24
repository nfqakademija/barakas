<?php

namespace App\Repository;

use App\Entity\Dormitory;
use App\Entity\DormitoryChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DormitoryChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method DormitoryChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method DormitoryChange[]    findAll()
 * @method DormitoryChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DormitoryChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DormitoryChange::class);
    }

    public function findCurrentUserDormitory($id)
    {
        $entityManager = $this->getEntityManager();

        $dormitoryRepo = $entityManager->getRepository(Dormitory::class);

        $dormitory = $dormitoryRepo->findOneBy(['id' => $id]);

        return $dormitory;

    }

    private function findUserOrganisationDormitory($id)
    {
        $entityManager = $this->getEntityManager();

        $dormitoryRepo = $entityManager->getRepository(Dormitory::class);

        $dormitory = $dormitoryRepo->findOneBy(['id' => $id]);

        $dorms = $dormitoryRepo->findBy(['organisation_id' => $dormitory->getOrganisationId()]);

        return $dorms;

    }

    public function removeUserDormitoryFromArray($user, $userDormitoryId)
    {
        $dorms = $this->findUserOrganisationDormitory($userDormitoryId);

        $dormitoryToRemove = null;

        foreach ($user as $struct) {
            if ($user->getDormId() == $struct->getDormId()) {
                $dormitoryToRemove = $struct;
                break;
            }
        }

        $key = array_search($dormitoryToRemove, $dorms);
        unset($dorms[$key]);

        return $dorms;
    }
}
