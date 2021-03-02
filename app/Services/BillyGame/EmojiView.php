<?php

namespace App\Services\BillyGame;

use App\Services\Emojis\AbstractEmojiInterpreter;
use App\Services\StateGrid\StateGridInterface;

class EmojiView
{
    /**
     * Fill a grid with the given view.
     *
     * @param string $view
     * @param StateGridInterface $grid
     * @return void
     */
    public function fillGridFromView($view, StateGridInterface $grid)
    {
        $view = trim("\n", $view);

        /** @var AbstractEmojiInterpreter */
        $interpreter = app(StateEmojiInterpreter::class);

        $view = $interpreter->convertEmojiAliases($view);

        $rows = explode("\n", $view);

        $grid->setDimensions(strlen(reset($rows)), count($rows));

        foreach ($rows as $y => $row) {
            $columns = str_split($row);

            foreach ($columns as $x => $column) {
                $grid->setStateAt($x, $y, $interpreter->toName($column));
            }
        }
    }

    /**
     * Create a view from the grid.
     *
     * @param StateGridInterface $grid
     * @return string
     */
    public function createViewFromGrid(StateGridInterface $grid)
    {
        $width = $grid->getWidth();
        $height = $grid->getHeight();
        $view = '';

        /** @var AbstractEmojiInterpreter */
        $interpreter = app(StateEmojiInterpreter::class);


        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $view .= $interpreter->toEmoji($grid->getStateAt($x, $y));
            }

            if ($y < $height - 1) {
                $view .= "\n";
            }
        }

        return $view;
    }
}
