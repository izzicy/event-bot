<?php

namespace App\Services\MMGame;

use App\Services\MMGame\Collections\AggregateAssocTileCollection;
use App\Services\MMGame\Collections\AssocTileCollection;
use App\Services\MMGame\Conquerers\Conquerer;
use App\Services\MMGame\Contracts\PickableRepositoryInterface;
use App\Services\MMGame\Contracts\UserAssociatedTileInterface;
use App\Services\MMGame\Contracts\UserAssocTilesCollectionInterface;
use App\Services\StateGrid\StateGridInterface;
use App\Services\Users\DiscordUser;
use App\Services\Users\UserInterface;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;

class Factory
{
    /**
     * Create the user commands parser from discord messages.
     *
     * @param Message[] $messages
     * @param PickableRepositoryInterface $pickableRepository
     * @return UserCommandsParser
     */
    public function createUserCommandsParserFromDiscordMessages($messages, PickableRepositoryInterface $pickableRepository)
    {
    $parser = new UserCommandsParser($this, $pickableRepository);

        foreach ($messages as $message) {
            $author = $message->author;
            $user = ($author instanceof Member) ? $author->user :  $author;

            $splitContent = explode("\n", $message->content);

            foreach ($splitContent as $command) {
                $parser->addCommand(new DiscordUser($user), $command);
            }
        }

        return $parser;
    }

    /**
     * Create the conquerer.
     *
     * @param PickableRepositoryInterface $pickableRepository
     * @param StateGridInterface $grid
     * @param UserAssocTilesCollectionInterface $picks
     * @return Conquerer
     */
    public function createConquerer(PickableRepositoryInterface $pickableRepository, StateGridInterface $grid, UserAssocTilesCollectionInterface $picks)
    {
        return new Conquerer($this, $pickableRepository, $grid, $picks);
    }

    /**
     * Create a user associated tile.
     *
     * @param UserInterface $user
     * @param int $tileX
     * @param int $tileY
     * @return UserAssociatedTileInterface
     */
    public function createUserAssociatedTile(UserInterface $user, $tileX, $tileY)
    {
        return new UserAssociatedTile($user, $tileX, $tileY);
    }

    /**
     * Create an aggregate associated tile collection.
     *
     * @param UserAssocTilesCollectionInterface[] $collections
     * @return UserAssocTilesCollectionInterface
     */
    public function createAggreateAssocTileCollection($collections)
    {
        return new AggregateAssocTileCollection($collections);
    }

    /**
     * Create an associated tile collection.
     *
     * @param UserAssociatedTileInterface[] $assocTiles
     * @return UserAssocTilesCollectionInterface
     */
    public function createAssocTileCollection($assocTiles)
    {
        return new AssocTileCollection($assocTiles);
    }

    /**
     * Create the game drawer.
     *
     * @return GameDrawer
     */
    public function createGameDrawer()
    {
        return new GameDrawer($this);
    }

    /**
     * Create the mine distributer.
     *
     * @return MineDistributer
     */
    public function createMineDistributer()
    {
        return new MineDistributer();
    }
}
