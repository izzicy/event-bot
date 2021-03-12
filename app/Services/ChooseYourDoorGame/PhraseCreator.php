<?php

namespace App\Services\ChooseYourDoorGame;

use App\Services\Choices\ChoicesResultsInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

class PhraseCreator
{
    /**
     * Seperate the winners and the losers.
     *
     * @param Collection $users
     * @param ChoicesResultsInterface $choices
     * @param Collection $correctChoices
     * @return string
     */
    public function create($users, $choices, $correctChoices)
    {
        $usersByDoor = $this->mapUsersPerDoor($users, $choices);

        $message = '';
        $winningIndex = 0;
        $losingIndex = 0;

        $usersByDoor->each(function($users, $door) use ($correctChoices, &$message, &$winningIndex, &$losingIndex) {
            $users = collect($users);
            $isCorrectChoice = $correctChoices->contains($door);
            $usersItemsInSeries = $users->map(function($user) {
                return '**' . $user->getUsername() . '**';
            })->toItemsInSeries(true);

            if ($door === 'cheater') {
                Lang::choice('choose-your-door.cheater', $users->count(), [
                    'users' => $usersItemsInSeries,
                ]);
            } else if ($isCorrectChoice) {
                $message .= Lang::choice($this->getLocalisation('win-lines.' . $winningIndex), $users->count(), [
                    'users' => $usersItemsInSeries,
                ]);
                $winningIndex += 1;
            } else {
                $message .= Lang::choice($this->getLocalisation('lose-lines.' . $losingIndex), $users->count(), [
                    'users' => $usersItemsInSeries,
                ]);
                $losingIndex += 1;
            }

            $message  .= "\n";;
        });

        return $message;
    }

    /**
     * Map the users per door.
     *
     * @param Collection $users
     * @param ChoicesResultsInterface $choices
     * @return Collection
     */
    protected function mapUsersPerDoor($users, $choices)
    {
        return $users->mapToDictionary(function($user) use ($choices) {
            return [
                $choices->getChoiceOfUser($user) => $user,
            ];
        });
    }

    /**
     * Get the localisation.
     *
     * @param string $line
     * @return string
     */
    protected function getLocalisation($line)
    {
        return data_get(
            json_decode(
                file_get_contents(
                    config('choose-your-door-game.phrases-file')
                ),
                true
            ),
            $line,
            ''
        );
    }
}
