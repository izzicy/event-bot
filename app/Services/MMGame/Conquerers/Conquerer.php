<?php

namespace App\Services\MMGame\Conquerers;

use App\Services\MMGame\Contracts\PickableRepositoryInterface;
use App\Services\MMGame\Factory;
use App\Services\MMGame\UserTilePicksCollection;
use App\Services\StateGrid\StateGridInterface;
use App\Services\Users\UserInterface;
use Illuminate\Support\Collection;

class Conquerer
{
    /**
     * The factory.
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
     * The state grid.
     *
     * @var StateGridInterface
     */
    protected $grid;

    /**
     * The user picks collection.
     *
     * @var UserTilePicksCollection
     */
    protected $picks;

    /**
     * The queues per user.
     *
     * @var
     */
    protected $queues;

    /**
     * The discovered tiles.
     *
     * @var array
     */
    protected $discovered = [];

    /**
     * @param Factory $factory
     * @param PickableRepositoryInterface $pickableRepository
     * @param StateGridInterface $grid
     * @param UserTilePicksCollection $picks
     */
    public function __construct(Factory $factory, PickableRepositoryInterface $pickableRepository, StateGridInterface $grid, UserTilePicksCollection $picks)
    {
        $this->pickableRepository = $pickableRepository;
        $this->grid = $grid;
        $this->picks = $picks;
        $this->factory = $factory;

        $this->initializeQueues();
        $users = $this->getUsersFromPicks();

        while ($this->someQueuesAreNotEmpty()) {
            foreach ($users as $user) {
                if ($this->isQueueNotEmpty($user)) {
                    list($tileX, $tileY) = $this->dequeue($user);

                    $this->handleNeighbouringTiles($tileX, $tileY, $user);
                }
            }
        }
    }

    /**
     * Get the conquered picks.
     *
     * @return UserTilePicksCollection
     */
    public function getConqueredPicks()
    {
        $extraPicks = $this->factory->createUserTilePicksCollection();

        foreach ($this->discovered as $x => $rows) {
            foreach ($rows as $y => $user) {
                if ($this->picks->isPicked($x, $y) === false) {
                    $extraPicks->push(
                        $this->factory->createUserTilePick($user, $x, $y)
                    );
                }
            }
        }

        return $extraPicks->merge($this->picks);
    }

    /**
     * Initialize the queues.
     *
     * @return void
     */
    protected function initializeQueues()
    {
        foreach ($this->picks as $pick) {
            $this->enqueue($pick->getUser(), $pick->getX(), $pick->getY());
        }
    }

    /**
     * Get the users from the picks.
     *
     * @return Collection
     */
    protected function getUsersFromPicks()
    {
        $users = collect();

        foreach ($this->picks as $pick) {
            if ( ! $users->has($pick->getUser()->getId())) {
                $users->put($pick->getUser()->getId(), $pick->getUser());
            }
        }

        return $users;
    }

    /**
     * Handle the neighbouring tiles.
     *
     * @param int $tileX
     * @param int $tileY
     * @param UserInterface $user
     * @return void
     */
    protected function handleNeighbouringTiles($tileX, $tileY, UserInterface $user)
    {
        $neighbours = [
            [ $listX - 1, $listY ],
            [ $listX + 1, $listY ],
            [ $listX, $listY + 1 ],
            [ $listX, $listY - 1 ],
        ];

        foreach ($neighbours as $neighbour) {
            $this->handleTileNeighbour($neighbour[0], $neighbour[1], $user);
        }
    }

    /**
     * Handle the tile neighbour.
     *
     * @param int $tileX
     * @param int $tileY
     * @param UserInterface $user
     * @return void
     */
    protected function handleTileNeighbour($tileX, $tileY, UserInterface $user)
    {
        if ($this->pickableRepository->isPickable($tileX, $tileY, $user) && $this->isDiscovered($tileX, $tileY) === false) {
            $this->discoverTile($tileX, $tilyY, $user);
            $this->enqueue($user, $tileX, $tileY);
        }
    }

    /**
     * Some queues are not empty.
     *
     * @return bool
     */
    protected function someQueuesAreNotEmpty()
    {
        foreach ($this->queues as $queue) {
            if (empty($queue) === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the queue is not empty.
     *
     * @param UserInterface $user
     * @return boolean
     */
    protected function isQueueNotEmpty(UserInterface $user)
    {
        return empty($this->queues[$user->getId()]) === false;
    }

    /**
     * Enqueu the given tile for the given user.
     *
     * @param UserInterface $user
     * @param int $tileX
     * @param int $tileY
     * @return void
     */
    protected function enqueue(UserInterface $user, $tileX, $tileY)
    {
        $this->queues[$user->getId()][] = [$tileX, $tileY];
    }

    /**
     * Dequeue for the given user.
     *
     * @param UserInterface $user
     * @return array
     */
    protected function dequeue(UserInterface $user)
    {
        return array_unshift($this->queues[$user->getId()]);
    }

    /**
     * Check whether the given tile is discovered.
     *
     * @param int $tileX
     * @param int $tileY
     * @return boolean
     */
    protected function isDiscovered($tileX, $tileY)
    {
        return isset($this->discovered[$tileX][$tileY]);
    }

    /**
     * Set the tile to 'discovered'.
     *
     * @param int $tileX
     * @param int $tileY
     * @param UserInterface $discoveredBy
     * @return void
     */
    protected function discoverTile($tileX, $tileY, $discoveredBy)
    {
        $this->discovered[$tileX][$tileY] = $discoveredBy;
    }
}