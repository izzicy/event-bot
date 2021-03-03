<?php

namespace App\Services\MMGame;

use App\Services\MMGame\Contracts\PickableRepositoryInterface;
use App\Services\Users\UserInterface;

class UserCommandsParser
{
    /**
     * The commands with their user.
     *
     * @var array
     */
    protected $commandsWithUser = [];

    /**
     * The factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * The pickable repository.
     *
     * @var PickableRepositoryInterface
     */
    protected $pickableRepository;

    /**
     * The limit per user.
     *
     * @var int
     */
    protected $limit = INF;

    /**
     * @param PickableRepositoryInterface $pickableRepository
     */
    public function __construct(Factory $factory, PickableRepositoryInterface $pickableRepository)
    {
        $this->factory = $factory;
        $this->pickableRepository = $pickableRepository;
    }

    /**
     * Add a user command.
     *
     * @param UserInterface $user
     * @param string $command
     * @return $this
     */
    public function addCommand(UserInterface $user, string $command)
    {
        $this->commandsWithUser[] = [
            $user,
            $command,
        ];

        return $this;
    }

    /**
     * Set the limit per user.
     *
     * @param int $limit
     * @return $this
     */
    public function setLimitPerUser($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Create the user tile picks.
     *
     * @return UserTilePicksCollection
     */
    public function createUserTilePicks()
    {
        $countPerUser = collect();
        $userTilePicks = $this->factory->createUserTilePicksCollection();

        foreach ($this->commandsWithUser as $commandWithUser) {
            list($user, $command) = $commandWithUser;
            $userId = $user->getId();

            if ( ! $countPerUser->has($userId)) {
                $countPerUser->put($userId, 0);
            }

            $countPerUser[$userId] += 1;

            if ($countPerUser[$userId] >= $this->limit) {
                break;
            }

            if (preg_match('/(?P<x>\d+)\s?(:| |;|-|,|\.)\s?(?P<y>\d+)/', $command, $matches)) {
                $x = $matches['x'];
                $y = $matches['y'];

                if ($this->pickableRepository->isPickable($x, $y, $user)) {
                    $userTilePicks->push(
                        $this->factory->createUserTilePick($user, $x, $y)
                    );
                }
            }
        }

        return $this->filterDuplicatePicks($userTilePicks);
    }

    /**
     * Filter out the duplicate picks.
     *
     * @param UserTilePicksCollection $collection
     * @return UserTilePicksCollection
     */
    protected function filterDuplicatePicks(UserTilePicksCollection $collection)
    {
        $chosenTiles = collect();

        return $collection->filter(function($pick) use ($chosenTiles) {
            $coords = $pick->getX() . ':' . $pick->getY();

            if ($chosenTiles->has($coords)) {
                return false;
            }

            $chosenTiles->put($coords, true);

            return true;
        });
    }
}
