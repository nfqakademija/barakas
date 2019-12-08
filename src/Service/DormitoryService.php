<?php


namespace App\Service;

use DateTime;

class DormitoryService
{

    public function calculateRewardPoints(DateTime $created_at, int $maxPoints): int
    {
        $currentTime =  new DateTime();
        $currentTime = $currentTime->getTimestamp();
        $created_at = $created_at->getTimestamp();
        $minutes = ($currentTime-$created_at)/60;
        $minutes = intval($minutes);
        $points  = $maxPoints - $minutes;
        if ($points<=0) {
            return 1;
        }
        return $points;
    }
}
