<?php

declare(strict_types=1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use SocialPost\Hydrator\FictionalPostHydrator;
use Statistics\Builder\ParamsBuilder;
use Statistics\Calculator\Factory\StatisticsCalculatorFactory;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;
use Statistics\Service\StatisticsService;

/**
 * Class TestAveragePostPerUser
 */
class TestAveragePostPerUser extends TestCase
{
    private $hydrator;

    public function setUp(): void
    {
        $this->hydrator = new FictionalPostHydrator();
    }

    /**
     * @test
     */
    public function testAveragePostPerUserForEmptyResponses(): void
    {
        $paramsTo = $this->createStub(ParamsTo::class);
        $paramsTo->method('getStatName')->willReturn(StatsEnum::AVERAGE_POST_NUMBER_PER_USER);

        $statsService = new StatisticsService(new StatisticsCalculatorFactory());
        $stats = $statsService->calculateStats((function () {yield; })(), [$paramsTo]);

        $children = $stats->getChildren();
        $averagePostPerUser = end($children);

        $this->assertEquals($averagePostPerUser->getName(), 'average-posts-per-user');
        $this->assertEquals($averagePostPerUser->getValue(), '0');
    }

    /**
     * @test
     */
    public function testAveragePostPerUser(): void
    {
        $date = \DateTime::createFromFormat('F, Y', 'August, 2018');
        $params = ParamsBuilder::reportStatsParams($date);

        $statsService = new StatisticsService(new StatisticsCalculatorFactory());
        $stats = $statsService->calculateStats($this->getTestPostsTraversal(), $params);

        $children = $stats->getChildren();
        $averagePostPerUser = end($children);

        $this->assertEquals($averagePostPerUser->getName(), 'average-posts-per-user');
        $this->assertEquals($averagePostPerUser->getValue(), '1.5');
    }

    private function getTestPostsTraversal(): \Traversable
    {
        $rawJsonData = '{
            "data": {
                "page": 1,
                "posts": [
                    {
                        "id": "post5b6ed7aeeb572_d6a654ac",
                        "from_name": "Regenia Boice",
                        "from_id": "user_13",
                        "message": "belong epicalyx sound recruit hate kinship jealous baby pattern absent crew literature option indulge dirty permission parachute magnetic pit element graphic hell delay alcohol belief second message therapist detective triangle berry cage quote delete ally straw introduction wrist fixture smoke manage exact trail discourage village awful kit deprive line omission galaxy tribe velvet kitchen snow interference final horseshoe development culture introduction stable snow suffer mean fan unaware feminine approval housing depression candle crop body swipe railroad sanctuary feature water consumption water book lake pillow space complex",
                        "type": "status",
                        "created_time": "2018-08-11T06:38:54+00:00"
                    },
                    {
                        "id": "post5b6ed7aeeb613_37c3a760",
                        "from_name": "Isidro Schuett",
                        "from_id": "user_16",
                        "message": "mother drop falsify describe college area blue jean dressing fuss hell barrier export escape linen museum flat referee instal jurisdiction execute instrument part fuss waist helmet friend rhythm cord extend plagiarize thinker sister dominant basket dignity value dorm fill marsh angel publisher spend album reconcile charter convince bed quest housing failure landowner",
                        "type": "status",
                        "created_time": "2018-08-11T02:42:39+00:00"
                    },
                    {
                        "id": "post5b6ed7aeeb613_37c3a760",
                        "from_name": "Isidro Schuett",
                        "from_id": "user_16",
                        "message": "mother drop falsify describe college area blue jean dressing fuss hell barrier export escape linen museum flat referee instal jurisdiction execute instrument part fuss waist helmet friend rhythm cord extend plagiarize thinker sister dominant basket dignity value dorm fill marsh angel publisher spend album reconcile charter convince bed quest housing failure landowner",
                        "type": "status",
                        "created_time": "2018-08-11T02:42:39+00:00"
                    }
                ]
            }
        }';

        $response = json_decode($rawJsonData, true);
        $posts = $response['data']['posts'] ?? [];

        foreach ($posts as $postData) {
            yield $this->hydrator->hydrate($postData);
        }
    }
}
