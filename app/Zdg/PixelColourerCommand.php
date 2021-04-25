<?php

namespace App\Zdg;

use App\Services\Messages\Contracts\MessageHandlerInterface;
use App\Services\Users\UserInterface;
use App\Zdg\Contracts\GameInterface;
use App\Zdg\Contracts\PixelInterface;
use Spatie\Color\Exceptions\InvalidColorValue;
use Spatie\Color\Factory;
use Spatie\Color\Hex;
use Spatie\Color\Hsl;

class PixelColourerCommand implements MessageHandlerInterface
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

    /** @inheritDoc */
    public function handleMessage($message)
    {
        $user = $message->getUser();
        $content = $message->getMessage();

        // /(colour|color|paint|pixel)(\s+(?P<x>\d+)(,|\s)+(?P<y>\d+)\s+(?P<modifier>dark|light)?\s*(?P<choices>[a-z0-9 #]+)\s*,?)+/i
        // /(colour|color|paint|pixel)(\s+\d+(,|\s)+\d+\s+(dark|light)?\s*[a-z0-9 #]+\s*,?)+/i

        if (preg_match_all('/(?P<commands>(colour|color|paint|pixel)(\s+\d+(,|\s)+\d+\s+(dark|light)?\s*[a-z0-9 #]+ *(,( and)?)?)+)/i', $content, $matches)) {
            foreach ($matches['commands'] as $command) {
                preg_match_all('/(?P<x>\d+)(,|\s)+(?P<y>\d+)\s+(?P<modifier>dark|light)?\s*(?P<choices>[a-z0-9 #]+) *(,( and)?)?/i', $command, $matches);

                foreach ($matches['choices'] as $key => $choice) {
                    $modifier = $matches['modifier'][$key];

                    // substract one since counting for the user starts at one
                    $x = $matches['x'][$key] - 1;
                    $y = - $matches['y'][$key];

                    $this->paintPixel($user, $modifier, $x, $y, $choice);
                }
            }
        } else if (preg_match_all('/(?P<commands>(colour|color|paint|pixel)(\s+\d+(,|\s)+\d+ *,?( and)?)+ +(?P<modifier>dark|light)?\s*(?P<choice>[a-z0-9 #]+))/i', $content, $matches)) {
            foreach ($matches['commands'] as $key => $command) {
                $modifier = $matches['modifier'][$key];
                $choice = $matches['choice'][$key];

                preg_match_all('/(?P<x>\d+)(,|\s)+(?P<y>\d+) *(,( and)?)?/i', $command, $matches);

                foreach ($matches['x'] as $key => $x) {
                    // substract one since counting for the user starts at one
                    $x = $matches['x'][$key] - 1;
                    $y = - $matches['y'][$key];

                    $this->paintPixel($user, $modifier, $x, $y, $choice);
                }
            }
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
        $width = $game->width;
        $height = $game->height;

        $indexes = [];
        $colorByIndex = [];
        $userByIndex = [];

        foreach ($this->choices as $x => $choices) {
            foreach ($choices as $y => $choice) {
                $user = $this->usersPerChoice[$x][$y];

                $realX = $x < 0 ? $x + $game->getWidth() : $x;
                $realY = $y < 0 ? $y + $game->getHeight() : $y;

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

    /**
     * Paint the given pixel.
     *
     * @param UserInterface $user
     * @param string $modifier
     * @param string|int $x
     * @param string|int $y
     * @param string $choice
     * @return void
     */
    protected function paintPixel($user, $modifier, $x, $y, $choice)
    {
        $maxPicks = config('zdg.moves-per-user');

        if (empty($this->picksPerUser[$user->getId()])) {
            $this->picksPerUser[$user->getId()] = 0;
        }

        $normalizedChoice = preg_replace('/[^a-z0-9]/', '', strtolower($choice));
        $colour = config('zdg.colours.' . $normalizedChoice, function() use ($choice) {
            try {
                if (preg_match('/^([0-9a-f]{6}|[0-9a-f]{3})$/i', $choice)) {
                    return (string) Factory::fromString('#' . $choice)->toHex();
                }

                return (string) Factory::fromString($choice)->toHex();
            }
            catch (InvalidColorValue $e) {
                return null;
            }
        });

        if (
            $colour
            && $this->picksPerUser[$user->getId()] < $maxPicks
            && empty($this->choices[$x][$y])
        ) {
            if ($modifier) {
                $colour = $this->adjustBrightness($colour, $modifier === 'dark' ? -20 : 20);
            }

            $this->choices[$x][$y] = $colour;
            $this->usersPerChoice[$x][$y] = $user;
            $this->picksPerUser[$user->getId()] += 1;
        }
    }

    /**
     * Adjust the color brightness
     *
     * @param string $hexCode
     * @param int $adjustPercent
     * @return string
     */
    protected function adjustBrightness($hexCode, $adjustPercent) {
        $hsl = Hex::fromString($hexCode)->toHsl();

        $lightness = max(0, min(100, $hsl->lightness() + $adjustPercent));

        return (string) (new Hsl($hsl->hue(), $hsl->saturation(), $lightness))->toHex();
    }

}
