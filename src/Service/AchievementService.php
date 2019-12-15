<?php


namespace App\Service;

use App\Entity\Achievement;
use App\Entity\AchievementType;
use App\Entity\Award;

class AchievementService extends Service
{
    private function giveAchievement($achievementId, $user): void
    {
        $achievementRepo = $this->getRepository(Achievement::class);

        $achievement = $achievementRepo->find($achievementId);

        $award = new Award();
        $award->setUser($user);
        $award->setAchievement($achievement);

        $this->entityManager->persist($award);
        $this->entityManager->flush();
    }

    public function giveFirstAidAchievement($user)
    {
        $this->giveAchievement(AchievementType::firstAid()->id(), $user);
    }

    public function giveTenHelpAchievement($user)
    {
        $this->giveAchievement(AchievementType::tenHelpProvided()->id(), $user);
    }

    public function giveTwentyHelpAchievement($user)
    {
        $this->giveAchievement(AchievementType::twentyHelpProvided()->id(), $user);
    }

    public function giveOneThousandPointsAchievement($user)
    {
        $this->giveAchievement(AchievementType::thousandPoints()->id(), $user);
    }

    public function giveTwoThousandPointsAchievement($user)
    {
        $this->giveAchievement(AchievementType::twoThousandPoints()->id(), $user);
    }

    public function giveFiveThousandPointsAchievement($user)
    {
        $this->giveAchievement(AchievementType::fiveThousandPoints()->id(), $user);
    }

    public function giveTenThousandPointsAchievement($user)
    {
        $this->giveAchievement(AchievementType::tenThousandPoints()->id(), $user);
    }

    public function giveTenMessagesAchievement($user)
    {
        $this->giveAchievement(AchievementType::tenMessages()->id(), $user);
    }

    public function giveTwentyMessagesAchievement($user)
    {
        $this->giveAchievement(AchievementType::twentyMessages()->id(), $user);
    }

    public function giveThirtyMessagesAchievement($user)
    {
        $this->giveAchievement(AchievementType::thirtyMessages()->id(), $user);
    }
}
