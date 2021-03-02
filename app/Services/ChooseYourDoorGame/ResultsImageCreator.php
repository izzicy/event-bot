<?php

namespace App\Services\ChooseYourDoorGame;

use App\Services\Choices\ChoicesResultsInterface;
use Illuminate\Support\Collection;
use Intervention\Image\ImageManagerStatic as Image;

class ResultsImageCreator
{
    /**
     * Create the response image.
     *
     * @param Collection $users
     * @param ChoicesResultsInterface $choices
     * @param Collection $correctChoices
     * @param int $numberOfDoors
     * @return string
     */
    public function create($users, $choices, $correctChoices, $numberOfDoors)
    {
        $background = Image::make(storage_path('app/choose-your-door/background.png'));
        $doorImage = Image::make(storage_path('app/choose-your-door/door.png'));
        $offsetY = 74;

        $backgroundHeight = $background->getHeight();
        $backgroundWidth = $background->getWidth();

        $ratio = $doorImage->getWidth() / $doorImage->getHeight();

        $doorHeight = round($backgroundHeight * 0.5);
        $doorWidth = round($ratio * $doorHeight);
        $doorImage->resize($doorWidth, $doorHeight);

        $userPerDoor = $this->mapUsersPerDoor($users, $choices);

        for ($i = 0; $i < $numberOfDoors; $i += 1) {
            $doorX = round($backgroundWidth / ($numberOfDoors + 1) * ($i + 1) - ($doorWidth / 2));
            $doorY = round($backgroundHeight - $doorHeight - $offsetY);

            $background->insert(
                $doorImage,
                'top-left',
                $doorX,
                $doorY
            );

            $this->insertEmoji($background, $i, $correctChoices, $doorX, $doorY, $doorWidth, $doorHeight);
            $this->insertUserAvatars(
                $background,
                collect($userPerDoor['door-' . ($i + 1)] ?? null),
                $doorX,
                $doorY,
                $doorWidth,
                $doorHeight
            );
        }

        $path = tempnam(sys_get_temp_dir(), 'image') . '.png';

        $background->save($path);

        return $path;
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
     * Insert a emoji.
     *
     * @param \Intervention\Image\Image $background
     * @param int|string $i
     * @param Collection $correctChoices
     * @param int $doorX
     * @param int $doorY
     * @param int $doorWidth
     * @param int $doorHeight
     * @return void
     */
    protected function insertEmoji($background, $i, $correctChoices, $doorX, $doorY, $doorWidth, $doorHeight)
    {
        $emoji = Image::make(
            $this->doorIndexIsWinning($i, $correctChoices)
            ? $this->getWinEmoji()
            : $this->getLoseEmoji()
        );

        $emojiWidth = $doorWidth * 0.5;
        $emojiX = round($doorX + ($emojiWidth / 2));
        $emojiY = round($doorY - $emojiWidth - $doorHeight * 0.1);

        $emoji->resize($emojiWidth, $emojiWidth);

        $background->insert(
            $emoji,
            'top-left',
            $emojiX,
            $emojiY
        );
    }

    /**
     * Insert the user avatars.
     *
     * @param \Intervention\Image\Image $background
     * @param Collection $users
     * @param int $doorX
     * @param int $doorY
     * @param int $doorWidth
     * @param int $doorHeight
     * @return void
     */
    protected function insertUserAvatars($background, $users, $doorX, $doorY, $doorWidth, $doorHeight)
    {
        $profileMask = Image::make(storage_path('app/choose-your-door/profile.png'));
        $userSize = floor($doorWidth * 0.333);

        $profileMask->resize($userSize, $userSize);

        foreach ($users as $index => $user) {
            $userImage = Image::make($user->getAvatar());

            $x = $index % 3;
            $y = floor($index / 3);

            $userImage->resize($userSize, $userSize);
            $userImage->mask($profileMask);
            $userX = round($doorX + $userSize * $x);
            $userY = round($doorY + $doorHeight + $userSize * $y);

            $background->insert(
                $userImage,
                'top-left',
                $userX,
                $userY
            );
        }
    }

    /**
     * Check if the given door index is winning.
     *
     * @param int|string $index
     * @param Collection $correctChoices
     * @return bool
     */
    protected function doorIndexIsWinning($index, $correctChoices)
    {
        return $correctChoices->contains('door-' . ($index + 1));
    }

    /**
     * Get a win emoji file path.
     *
     * @return string
     */
    protected function getWinEmoji()
    {
        $emojis = config('choose-your-door-game.win-emojis');

        return collect($emojis)->random();
    }

    /**
     * Get a win emoji file path.
     *
     * @return string
     */
    protected function getLoseEmoji()
    {
        $emojis = config('choose-your-door-game.lose-emojis');

        return collect($emojis)->random();
    }
}
