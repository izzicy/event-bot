<?php

namespace Tests\Unit;

use App\Services\BillyGame\BillyGame;
use App\Services\StateGrid\MemoryStateGrid;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BillyGameTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_moves_billy_to_the_correct_position()
    {
        $grid = new MemoryStateGrid();
        $grid->setDimensions(10, 10);

        $grid->setStateAt(5, 5, 'billy');
        $grid->setStateAt(6, 7, 'empty');

        $game = new BillyGame($grid);

        $game->moveBilly(1, 2);

        $this->assertEquals('billy', $grid->getStateAt(6, 7));
        $this->assertEquals('empty', $grid->getStateAt(5, 5));
    }
}
