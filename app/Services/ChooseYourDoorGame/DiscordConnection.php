<?php

namespace App\Services\ChooseYourDoorGame;

use Discord\Discord;
use Discord\Helpers\Collection;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;
use Illuminate\Pipeline\Pipeline;
use React\Promise\Deferred;
use React\Promise\Promise;

class DiscordConnection
{
    /**
     * Post a game update.
     *
     * @param Discord $discord
     * @param string $channelId
     * @param string $doorsPath
     * @param int $doorCount
     * @return Promise
     */
    public function postChoiceBooth(Discord $discord, $channelId, $doorsPath, $doorCount)
    {
        $deferred = new Deferred();

        $channel = $discord->getChannel($channelId);

        $message = 'Choose door ';

        if ($doorCount > 1) {
            $message .= collect(range(1, $doorCount - 1))->join(', ') . ' or ' . $doorCount;
        } else {
            $message = 'Choose door.';
        }

        $channel->sendFile($doorsPath, null, $message)->then(function(Message $message) use ($deferred, $doorCount) {
            $messageId = $message->id;

            /** @var AbstractEmojiInterpreter */
            $interpreter = app(ChoiceEmojiInterpreter::class);

            $pipelines = [];

            $emojis = collect($interpreter->getEmojis())->take($doorCount);

            foreach ($emojis as $emoji) {
                $pipelines[] = function(Message $message, $next) use ($emoji) {
                    $message->react($emoji)->then(function() use ($message, $next) {
                        $next($message);
                    });
                };
            }

            (new Pipeline(app()))
                ->send($message)
                ->through($pipelines)
                ->then(function() use ($deferred, $messageId) {
                    $deferred->resolve($messageId);
                });
        });

        return $deferred->promise();
    }

    /**
     * Get the user choices.
     *
     * @param Discord $discord
     * @param string $channelId
     * @param string|null $messageId
     * @return Promise
     */
    public function getUserChoices(Discord $discord, $channelId, $messageId = null)
    {
        $deferred = new Deferred();

        $channel = $discord->getChannel($channelId);

        if ($messageId) {
            $channel->getMessage(
                $messageId
            )->done(
                function ($message) use ($deferred, $discord) {
                    $this->handleVotedMessage($discord, $message, $deferred);
                }
            );
        } else {
            $channel->getMessageHistory([
                'limit' => 1,
            ])->done(function (Collection $messages) use ($deferred, $discord) {
                $this->handleVotedMessage($discord, $messages->first(), $deferred);
            });
        }

        return $deferred->promise();
    }

    /**
     * Post the lost users.
     *
     * @param Discord $discord
     * @param [type] $channelId
     * @param [type] $users
     * @return Promise
     */
    public function postLosers(Discord $discord, $channelId, $content, $imagePath)
    {
        $deferred = new Deferred();
        $channel = $discord->getChannel($channelId);

        $channel->sendFile($imagePath, null, $content)->then(function() use ($deferred) {
            $deferred->resolve();
        });

        return $deferred->promise();
    }

    /**
     * Handle the given voted message.
     *
     * @param Message $message
     * @param StateGrid $grid
     * @param Discord $discord
     * @return void
     */
    protected function handleVotedMessage(Discord $discord, Message $message, Deferred $deferred)
    {
        $reactionUsersPromises = [];
        $reactionEmojis = [];

        foreach ($message->reactions as $reaction) {
            if (app(ChoiceEmojiInterpreter::class)->hasEmoji($reaction->emoji->name) === false) {
                continue;
            }

            $query = "channels/{$reaction->channel_id}/messages/{$reaction->message_id}/reactions/".urlencode($reaction->emoji->name);

            $reactionEmojis[] = $reaction->emoji->name;
            $reactionUsersPromises[] = $discord->getHttpClient()->get($query)
                ->then(function ($response) use ($discord) {
                    $users = new Collection([], 'id', User::class);

                    foreach ((array) $response as $user) {
                        if ($existingUser = $discord->users->get('id', $user->id)) {
                            $users->push($existingUser);
                        } else {
                            $users->push(new User($discord, (array) $user, true));
                        }
                    }

                    return $users;
                });
        }

        if (empty($reactionUsersPromises)) {
            $deferred->resolve(new ChooseYourDoorChoices());
        }

        \React\Promise\all($reactionUsersPromises)
            ->then(function($reactionUsers) use ($deferred, $reactionEmojis) {
                $choices = new ChooseYourDoorChoices();

                foreach ($reactionUsers as $key => $users) {
                    $emojiName = $reactionEmojis[$key];

                    foreach ($users as $user) {
                        if ($user->bot !== true) {
                            $choices->addUserWithEmoji($user, $emojiName);
                        }
                    }
                }

                $deferred->resolve($choices);
            });
    }
}
