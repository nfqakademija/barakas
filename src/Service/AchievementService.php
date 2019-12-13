<?php


namespace App\Service;

use App\Entity\AchievementType;
use App\Entity\Award;
use Doctrine\ORM\EntityManagerInterface;

class AchievementService extends Service
{
    private function giveAchievement(int $achievementId)
    {
        $user = $this->getUser();

        $award = new Award();
        $award->setUser($user);
        $award->setAchievement($achievementId);

        $this->entityManager->persist($award);
        $this->entityManager->flush();
    }

    public function giveFirstAidAchievement()
    {
        $this->giveAchievement(AchievementType::firstAid()->id());
    }

    public function giveTenHelpAchievement()
    {
        $this->giveAchievement(AchievementType::tenHelpProvided()->id());
    }

    public function giveTwentyHelpAchievement()
    {
        $this->giveAchievement(AchievementType::twentyHelpProvided()->id());
    }

    public function giveOneThousandPointsAchievement()
    {
        $this->giveAchievement(AchievementType::thousandPoints()->id());
    }

    public function giveTwoThousandPointsAchievement()
    {
        $this->giveAchievement(AchievementType::twoThousandPoints()->id());
    }

    public function giveFiveThousandPointsAchievement()
    {
        $this->giveAchievement(AchievementType::fiveThousandPoints()->id());
    }

    public function giveTenThousandPointsAchievement()
    {
        $this->giveAchievement(AchievementType::tenThousandPoints()->id());
    }

    public function giveTenMessagesAchievement()
    {
        $this->giveAchievement(AchievementType::tenMessages()->id());
    }

    public function giveTwentyMessagesAchievement()
    {
        $this->giveAchievement(AchievementType::twentyMessages()->id());
    }

    public function giveThirtyMessagesAchievement()
    {
        $this->giveAchievement(AchievementType::thirtyMessages()->id());
    }
}
