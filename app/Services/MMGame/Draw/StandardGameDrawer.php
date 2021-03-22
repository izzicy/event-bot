<?php

namespace App\Services\MMGame\Draw;

class StandardGameDrawer extends AbstractGameDrawer
{
    /**
     * @inheritdoc
     */
    public function draw()
    {
        $game = $this->game;

        /** @var StateGridInterface */
        $grid = $game->grid;

        $width = $grid->getWidth();
        $height = $grid->getHeight();

        $collection = $this->factory->createAssocTileCollection($game->conquered);

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                $stateAtCoords = $grid->getStateAt($x, $y);

                if (
                    preg_match('/^nearby_(?P<minecount>\d+)$/', $stateAtCoords, $matches)
                    && (
                        $collection->hasTileAt($x, $y)
                        || $this->nearbyHasBeenConquered($x, $y, $grid, $collection)
                    )
                ) {
                    if ($collection->hasTileAt($x, $y)) {
                        $assocTile = $collection->getTileAt($x, $y);

                        $this->drawColoredSquareAt($x, $y, $assocTile->getUser());
                    } else {
                        $this->drawBlackSquareAt($x, $y);
                    }

                    $this->drawCountAt($x, $y, $matches['minecount']);
                } else if ($collection->hasTileAt($x, $y)) {
                    $assocTile = $collection->getTileAt($x, $y);

                    $this->drawColoredSquareAt($x, $y, $assocTile->getUser());
                } else {
                    $this->drawEmptyTileAt($x, $y);
                }
            }
        }

        $path = tempnam(sys_get_temp_dir(), 'image') . '.png';

        $this->canvas->save($path);

        return $path;
    }
}
