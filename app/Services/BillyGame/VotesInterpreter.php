<?php

namespace App\Services\BillyGame;

use App\Services\StateGrid\MemoryStateGrid;
use App\Services\StateGrid\StateGridInterface;
use App\Services\StateGrid\StateUtil;
use App\Services\Votes\VotesResultsInterface;

class VotesInterpreter
{
    /**
     * Interpret the given votes.
     *
     * @param StateGridInterface $grid
     * @param VotesResultsInterface $votes
     * @return void
     */
    public function interpret(StateGridInterface $grid, VotesResultsInterface $votes)
    {
        $billyPositionBoard = (new MemoryStateGrid())->setDimensions($grid->getWidth(), $grid->getHeight());
        $snakePositionBoard = clone $billyPositionBoard;
        $mousePositionBoard = clone $billyPositionBoard;

        $width = $grid->getWidth();
        $height = $grid->getHeight();

        for ($y = 0; $y < $height; $y += 1) {
            for ($x = 0; $x < $width; $x += 1) {
                if ($grid->getStateAt($x, $y) === 'billy') {
                    $vote = $votes->getRandomAtPlace(0);
                    $delta = $this->determineDelta($vote);

                    $grid->setStateAt($x, $y, 'empty');
                    $billyPositionBoard->setStateAt($x + $delta[0], $y + $delta[1], 'billy');
                }

                if ($grid->getStateAt($x, $y) === 'snake') {
                    $vote = $votes->getRandomAtPlace(1);
                    $delta = $this->determineDelta($vote);

                    $grid->setStateAt($x, $y, 'empty');
                    $snakePositionBoard->setStateAt($x + $delta[0], $y + $delta[1], 'snake');
                }

                if ($grid->getStateAt($x, $y) === 'mouse') {
                    $vote = $votes->getRandomAtPlace(2);
                    $delta = $this->determineDelta($vote);

                    $grid->setStateAt($x, $y, 'empty');
                    $mousePositionBoard->setStateAt($x + $delta[0], $y + $delta[1], 'mouse');
                }
            }
        }

        $billyPosition = StateUtil::findState($billyPositionBoard, 'billy');

        $grid->setStateAt($billyPosition[0], $billyPosition[1], 'billy');
    }

    /**
     * Determine the delta by the given command.
     *
     * @param string|null $command
     * @return array
     */
    protected function determineDelta($command)
    {
        switch($command) {
            case 'go-up':
                return [0, -1];
            break;
            case 'go-left':
                return [-1, 0];
            break;
            case 'go-right':
                return [-1, 0];
            break;
            case 'go-down':
                return [0, 1];
            break;
            case 'go-down-right':
                return [1, 1];
            break;
            case 'go-down-left':
                return [-1, 1];
            break;
            case 'go-up-right':
                return [1, -1];
            break;
            case 'go-up-left':
                return [-1, -1];
            break;
            default:
                return [0, 0];
            break;
        }
    }
}
