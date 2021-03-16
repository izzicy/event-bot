<?php

namespace App\Services\MMGame;

use App\Services\MMGame\Contracts\PickableRepositoryInterface;
use App\Services\MMGame\Contracts\UserAssocTilesCollectionInterface;
use App\Services\Users\UserInterface;
use Illuminate\Support\Collection;

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
     * @return UserAssocTilesCollectionInterface
     */
    public function createUserTilePicks()
    {
        $countPerUser = collect();
        $userTilePicks = collect();
        $taken = [];

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
                $x = (int) $matches['x'];
                $y = (int) $matches['y'];

                if ($this->pickableRepository->isPickable($x, $y, $user) && empty($taken[$x][$y])) {
                    $taken[$x][$y] = true;
                    $userTilePicks->push(
                        $this->factory->createUserAssociatedTile($user, $x, $y)
                    );
                }
            }
        }

        return $this->factory->createAssocTileCollection($this->filterDuplicatePicks($userTilePicks)->all());
    }

    /**
     * Filter out the duplicate picks.
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function filterDuplicatePicks(Collection $collection)
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
