<?php

namespace App\Mmg\Commands;

use App\Mmg\Contracts\CommandInterface;
use App\Mmg\Contracts\FactoryInterface;
use App\Mmg\Contracts\GameInterface;
use App\Mmg\Testers\UserMovesTester;
use App\Services\Users\UserInterface;

class PickTileCommand implements CommandInterface
{
    /** @var FactoryInterface */
    protected $factory;

    /**
     * An associated array with coordinate picks and user ids as the key.
     *
     * @var array[]array[]int[]
     */
    protected $picks = [];

    /**
     * An associative array of users.
     *
     * @var UserInterface[]
     */
    protected $users = [];

    /**
     * Pick tile command constructor.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /** @inheritdoc */
    public function handleMessage($message)
    {
        $user = $message->getUser();

        if (preg_match_all('/(pick|choose|tile|conquer)\s+(?P<x>\d+)(,|\s)+(?P<y>\d+)/', $message->getMessage(), $matches)) {
            if (empty($this->picks[$user->getId()])) {
                $this->users[] = $user;
            }

            foreach ($matches['x'] as $key => $x) {
                $y = $matches['y'][$key] ?? 0;

                $this->picks[$user->getId()][] = [$x, $y];
            }
        }
    }

    /** @inheritdoc */
    public function operateGame($game)
    {
        $picks = $this->retrieveValidPicks($game);

        if ($game->hasInitialized() === false) {
            $this->initializeGame($game, $picks);
        }

        $conquerer = $this->factory->createConquerer($picks, $this->users);

        $conquerer->operateGame($game);
    }

    /**
     * Retrieve the valid picks.
     *
     * @param GameInterface $game
     * @return array[]array[]int[]
     */
    protected function retrieveValidPicks($game)
    {
        $validPicks = [];
        $userMovesTester = new UserMovesTester;

        foreach ($game->getTiles() as $tile) {
            $userMovesTester->testTile($tile);
        }

        foreach ($this->users as $user) {
            $userId = $user->getId();
            $maxPicks = $userMovesTester->getNumberOfMoves($user);

            foreach ($this->picks[$userId] as $pick) {
                if (empty($validPicks[$userId])) {
                    $validPicks[$userId] = [];
                }

                if (count($validPicks[$userId]) > $maxPicks) {
                    continue;
                }

                if ($game->hasTileAt($pick[0], $pick[1]) === false) {
                    continue;
                }

                $tile = $game->getTileAt($pick[0], $pick[1]);

                if ($tile->getConquerer() != null) {
                    continue;
                }

                $validPicks[$userId][] = $pick;
            }
        }

        return $validPicks;
    }

    /**
     * Initalize the game.
     *
     * @param GameInterface $game
     * @param array[]array[]int[] $picks
     * @return void
     */
    protected function initializeGame($game, $picks)
    {
        $distributer = $this->factory->createMineDistributer(collect($picks)->flatten(1)->all(), $game->getMineCount());

        $distributer->operateGame($game);

        foreach ($game->getTiles() as $tile) {
            if ($tile->getState() === 'unknown') {
                $tile->setState('empty');
            }
        }

        $game->initialize();
    }
}
