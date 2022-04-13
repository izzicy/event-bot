<?php

namespace App\Zdg;

use App\Services\Messages\Contracts\MessageHandlerInterface;
use App\Services\Users\UserInterface;
use Spatie\Color\Exceptions\InvalidColorValue;
use Spatie\Color\Factory;
use Spatie\Color\Hex;
use Spatie\Color\Hsl;

class PixelColourerCommand implements MessageHandlerInterface
{
    /** @var ChoicesAggregate */
    protected $choices;

    /**
     * Construct a new pixel colourer.
     *
     * @param ChoicesAggregate $choices
     */
    public function __construct(ChoicesAggregate $choices)
    {
        $this->choices = $choices;
    }

    /** @inheritDoc */
    public function handleMessage($message)
    {
        if (count($message->getImageAttachments()) > 0) {
            return;
        }

        $user = $message->getUser();
        $content = $message->getMessage();

        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $this->handlePotentialCommandLine($user, $line);
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

        if ($colour) {
            if ($modifier) {
                $colour = $this->adjustBrightness($colour, $modifier === 'dark' ? -20 : 20);
            }

            $this->choices->paintPixel($user, $x, $y, $colour);
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
