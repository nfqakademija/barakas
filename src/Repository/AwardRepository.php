<?php

namespace App\Repository;

use App\Entity\AchievementType;
use App\Entity\Award;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Award|null find($id, $lockMode = null, $lockVersion = null)
 * @method Award|null findOneBy(array $criteria, array $orderBy = null)
 * @method Award[]    findAll()
 * @method Award[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AwardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Award::class);
    }

    public function findTenMessagesAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::tenMessages()->id()
        ]);
    }

    public function findTwentyMessagesAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::twentyMessages()->id()
        ]);
    }

    public function findThirtyMessagesAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::thirtyMessages()->id()
        ]);
    }

    public function findFirstAidAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::firstAid()->id()
        ]);
    }

    public function findTenHelpAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::tenHelpProvided()->id()
        ]);
    }

    public function findTwentyHelpAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::twentyHelpProvided()->id()
        ]);
    }

    public function findOneThousandPointsAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::thousandPoints()->id()
        ]);
    }

    public function findTwoThousandPointsAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::twoThousandPoints()->id()
        ]);
    }

    public function findFiveThousandPointsAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::fiveThousandPoints()->id()
        ]);
    }

    public function findTenThousandPointsAchievementByUser($user)
    {
        return $this->findBy([
            'user' => $user,
            'achievement' => AchievementType::tenThousandPoints()->id()
        ]);
    }
}
