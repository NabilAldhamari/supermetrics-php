<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

/**
 * Class Calculator
 */
class AveragePostPerUser extends AbstractCalculator
{
    protected const UNITS = 'posts';

    /**
     * @var int
     */
    private $totalPostsPerMonth = 0;

    /**
     * @var array
     */
    private $totalUsersPerMonth = [];

    /**
     * @param SocialPostTo $postTo
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        ++$this->totalPostsPerMonth;
        $this->totalUsersPerMonth[$postTo->getAuthorId()] = $postTo->getAuthorName();
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        $usersCount = count($this->totalUsersPerMonth);
        $value = $usersCount > 0
            ? $this->totalPostsPerMonth / $usersCount
            : 0;

        return (new StatisticsTo())->setValue(round($value, 2))->setUnits(self::UNITS);
    }
}
