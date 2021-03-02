<?php

namespace App\Services\MMGame;

use App\Services\MMGame\Contracts\PickableRepositoryInterface;
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
     * Create a tile picked by the user.
     *
     * @param UserInterface $user
     * @param int $tileX
     * @param int $tileY
     * @return UserTilePick
     */
    public function createUserTilePick(UserInterface $user, $tileX, $tileY)
    {
        return new UserTilePick($user, $tileX, $tileY);
    }

    /**
     * Create a new user tile pick collection.
     *
     * @param array $userTilePicks
     * @return UserTilePicksCollection
     */
    public function createUserTilePicksCollection(array $userTilePicks = [])
    {
        return new UserTilePicksCollection($userTilePicks);
    }
}
