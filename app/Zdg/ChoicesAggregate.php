<?php

namespace App\Zdg;

use App\Services\Users\UserInterface;
use App\Zdg\Contracts\GameInterface;
use Spatie\Color\Hex;

class ChoicesAggregate
{
    /**
     * The number of picks per user.
     *
     * @var int[]
     */
    protected $picksPerUser = [];

    /**
     * The chosen colours presented as hex.
     *
     * @var array[]string[]
     */
    protected $choices = [];

    /**
     * The users per choice.
     *
     * @var array[]UserInterface[]
     */
    protected $usersPerChoice = [];

    /**
     * Whether choices exist.
     *
     * @return boolean
     */
    public function hasChoices()
    {
        return empty($this->choices) === false;
    }

    /**
     * Paint the given pixel with the given colour.
     *
     * @param UserInterface $user
     * @param int $x
     * @param int $y
     * @param string $colour
     * @return void
     */
    public function paintPixel($user, $x, $y, $colour)
    {
        $maxPicks = config('zdg.moves-per-user');

        if (empty($this->picksPerUser[$user->getId()])) {
            $this->picksPerUser[$user->getId()] = 0;
        }

        // If the user wants to override a previously painted pixel
        // then allow that without subtracting from their pick count.
        if (
            isset($this->choices[$x][$y])
            && $this->usersPerChoice[$x][$y]->getId() === $user->getId()
        ) {
            $this->choices[$x][$y] = $colour;

        // Else if the pixel does not belong to this user then treat it as the following:
        } else if (
            $this->picksPerUser[$user->getId()] < $maxPicks
            && empty($this->choices[$x][$y])
        ) {
            $this->choices[$x][$y] = $colour;
            $this->usersPerChoice[$x][$y] = $user;
            $this->picksPerUser[$user->getId()] += 1;
        }
    }

    /**
     * Operate on the game instance.
     *
     * @param GameInterface $game
     * @return void
     */
    public function operateGame(GameInterface $game)
    {
        $width = $game->getWidth();
        $height = $game->getHeight();

        $indexes = [];
        $colorByIndex = [];
        $userByIndex = [];

        foreach ($this->choices as $x => $choices) {
            foreach ($choices as $y => $choice) {
                $user = $this->usersPerChoice[$x][$y];

                $realX = $x < 0 ? $x + $game->getWidth() : $x;
                $realY = $y < 0 ? $y + $game->getHeight() : $y;

                if ($realX >= $width || $realX < 0) {
                    continue;
                }

                if ($realY >= $height || $realY < 0) {
                    continue;
                }

                $index = $realY * $width + $realX;
                $indexes[] = $index;
                $colorByIndex[$index] = $choice;
                $userByIndex[$index] = $user;
            }
        }

        $pixels = $game->findPixels($indexes);

        foreach ($pixels as $pixel) {
            /** @var PixelInterface $pixel */

            $choice = $colorByIndex[$pixel->getIndex()];
            $user = $userByIndex[$pixel->getIndex()];
            $rgb = Hex::fromString($choice)->toRgb();

            $pixel->setPainter($user);
            $pixel->setRgb($rgb->red(), $rgb->green(), $rgb->blue());
        }
    }
}
