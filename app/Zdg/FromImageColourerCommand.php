<?php

namespace App\Zdg;

use App\Services\Messages\Contracts\MessageHandlerInterface;
use App\Services\Users\UserInterface;
use Intervention\Image\ImageManagerStatic;
use Spatie\Color\Exceptions\InvalidColorValue;
use Spatie\Color\Factory;
use Spatie\Color\Hex;
use Spatie\Color\Hsl;
use Spatie\Color\Rgb;

class FromImageColourerCommand implements MessageHandlerInterface
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
        if (count($message->getImageAttachments()) === 0) {
            return;
        }

        if ( ! preg_match('/paint +(at +)?(?P<x>\d+) +(?P<y>\d+)( +(?P<with_white>with +white))?/i', $message->getMessage(), $matches)) {
            return;
        }

        $baseX = $matches['x'] - 1;
        $baseY = - $matches['y'];
        $withWhite = $matches['with_white'] ?? false;

        $user = $message->getUser();
        $image = ImageManagerStatic::make(collect($message->getImageAttachments())->first()->getUrl());
        $height = $image->getHeight();

        foreach (range(0, $image->getWidth() - 1) as $x) {
            foreach (range(0, $image->getHeight() - 1) as $y) {
                $colour = $image->pickColor($x, $y);

                $canBePainted = collect($colour)->last() == 1;

                if ( ! $withWhite) {
                    $canBePainted = $canBePainted && $colour != [255, 255, 255, 1];
                }

                $colourString = (string) (new Rgb($colour[0], $colour[1], $colour[2]))->toHex();

                if ($canBePainted) {
                    $this->choices->paintPixel($user, $baseX + $x, $baseY - $height + $y + 1, $colourString);
                }
            }
        }
    }
}
