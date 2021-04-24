<?php

namespace Tests\Unit\Mmg;

use App\Mmg\Contracts\FactoryInterface;
use App\Mmg\GameRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MmgTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_create_mmg_games()
    {
        $width = $this->faker->numberBetween(4, 5);
        $height = $this->faker->numberBetween(4, 5);

        $game = app(GameRepository::class)->create($width, $height, 0);

        $this->assertEquals($game->tiles->count(), $width * $height);

        foreach ($game->tiles as $tile) {
            $this->assertNull($tile->getConquerer());
            $this->assertEquals('unknown', $tile->getState());
            $this->assertEquals(0, count($tile->getFlaggers()));
        }
    }

    /** @test */
    public function it_successfully_distributes_mines_in_a_given_game()
    {
        $width = $this->faker->numberBetween(4, 5);
        $height = $this->faker->numberBetween(4, 5);
        $mineCount = $this->faker->numberBetween(4, 14);

        $game = app(GameRepository::class)->create($width, $height, $mineCount);

        /** @var FactoryInterface */
        $factory = app(FactoryInterface::class);
        $distributer = $factory->createMineDistributer([], $mineCount);

        $distributer->operateGame($game);

        $retrievedMineCount = 0;

        foreach ($game->tiles as $tile) {
            if ($tile->getState() === 'mine') {
                $retrievedMineCount += 1;
            }
        }

        $this->assertEquals($mineCount, $retrievedMineCount);
    }
}
