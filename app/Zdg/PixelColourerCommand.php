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

        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $this->handlePotentialCommandLine($user, $line);
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

    /**
     * Handle the potential command line.
     *
     * @param UserInterface $user
     * @param string $line
     * @return void
     */
    protected function handlePotentialCommandLine($user, $line)
    {
        if (preg_match('/(paint|color|colour|pixel) *(?P<arguments>.*)/i', $line, $matches)) {
            $this->handleArgumentsString($user, $matches['arguments']);
        }
    }

    /**
     * Handle the arguments string.
     *
     * @param UserInterface $user
     * @param string $argumentsString
     * @return void
     */
    protected function handleArgumentsString($user, $argumentsString)
    {
        $arguments = preg_split('/(\W+and\W+)| *, */i', $argumentsString);
        $gatheredPixels = [];

        foreach ($arguments as $argument) {
            if (preg_match('/(?P<x1>\d+) +(?P<y1>\d+) +to +(?P<x2>\d+) +(?P<y2>\d+)( +(?P<modifier>dark|light)? *(?P<choice>[a-z0-9 #]+))?/i', $argument, $matches)) {
                $x1 = $matches['x1'] - 1;
                $y1 = - $matches['y1'];
                $x2 = $matches['x2'] - 1;
                $y2 = - $matches['y2'];
                $modifier = $matches['modifier'] ?? null;
                $choice = $matches['choice'] ?? null;

                foreach (range($x1, $x2) as $x) {
                    foreach (range($y1, $y2) as $y) {
                        $gatheredPixels[] = [$x, $y];
                    }
                }

                if ($choice) {
                    $this->paintPixels($user, $modifier, $gatheredPixels, $choice);
                }
            } else if (preg_match('/(?P<x>\d+) +(?P<y>\d+)( +(?P<modifier>dark|light)? *(?P<choice>[a-z0-9 #]+))?/i', $argument, $matches)) {
                $x = $matches['x'] - 1;
                $y = - $matches['y'];
                $modifier = $matches['modifier'] ?? null;
                $choice = $matches['choice'] ?? null;

                $gatheredPixels[] = [$x, $y];

                if ($choice) {
                    $this->paintPixels($user, $modifier, $gatheredPixels, $choice);
                }
            }
        }
    }

    /**
     * Paint the given pixels.
     *
     * @param UserInterface $user
     * @param string $modifier
     * @param array[]int[] $pixels
     * @param string $choice
     * @return void
     */
    protected function paintPixels($user, $modifier, $pixels, $choice)
    {
        foreach ($pixels as $pixel) {
            $this->paintPixel($user, $modifier, $pixel[0], $pixel[1], $choice);
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
