<?php

namespace App\Services\BillyGame;

use App\Services\Emojis\AbstractEmojiInterpreter;
use App\Services\Emojis\EmojiVotes;
use App\Services\StateGrid\StateGridInterface;
use Discord\Discord;
use Discord\Helpers\Collection;
use Discord\Parts\Channel\Message;
use GuzzleHttp\Promise\Promise;
use React\Promise\Deferred;

class DiscordConnection
{
    /**
     * Post a game update.
     *
     * @param Discord $discord
     * @param StateGridInterface $grid
     * @param string $channelId
     * @return Promise
     */
    public function postGameUpdate(Discord $discord, StateGridInterface $grid, $channelId)
    {
        $deferred = new Deferred();

        $channel = $discord->getChannel($channelId);

        $channel->sendMessage(
            app(EmojiView::class)->createViewFromGrid($grid),
        )->then(function(Message $message) use ($deferred) {
            $messageId = $message->id;
            // $this->info('The message id: ' . $message->id);

            /** @var AbstractEmojiInterpreter */
            $interpreter = app(VoteEmojiInterpreter::class);

            $reactions = [];

            foreach ($interpreter->getEmojis() as $emoji) {
                $reactions[] = $message->react($emoji);
            }

            \React\Promise\all($reactions)->then(function() use ($deferred, $messageId) {
                $deferred->resolve($messageId);
            });
        });

        return $deferred->promise();
    }

    /**
     * Get the game votes.
     *
     * @param Discord $discord
     * @param string $channelId
     * @param string $messageId
     * @return Promise
     */
    public function getGameVotes(Discord $discord, $channelId, $messageId = null)
    {
        $deferred = new Deferred();

        $channel = $discord->getChannel($channelId);

        if ($messageId) {
            $channel->getMessage(
                $messageId
            )->done(
                function ($message) use ($deferred) {
                    $this->handleVotedMessage($message, $deferred);
                }
            );
        } else {
            $channel->getMessageHistory([
                'limit' => 1,
            ])->done(function (Collection $messages) use ($deferred) {
                $this->handleVotedMessage($messages->first(), $deferred);
            });
        }

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
    protected function handleVotedMessage(Message $message, Deferred $deferred)
    {
        /** @var AbstractEmojiInterpreter */
        $interpreter = app(VoteEmojiInterpreter::class);

        $votes = new EmojiVotes($interpreter);

        foreach ($message->reactions as $reaction) {
            $votes->addEmoji($reaction->emoji->name, $reaction->count);
        }

        $votes->sortVotes();

        $deferred->resolve($votes);
    }
}
