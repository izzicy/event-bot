<?php

namespace App\Services\MMGame;

use App\Models\MultiplayerMinesweeper\MinesweeperGame;
use App\Services\StateGrid\StateGridInterface;
use App\Services\Users\UserInterface;
use App\Util\Intervention\ImageUtil;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

class GameDrawer
{
    /**
     * The factory instance.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * The user color cache.
     *
     * @var array
     */
    protected $userColorCache = [];

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Draw the minesweeper game.
     *
     * @param MinesweeperGame $game
     * @return string
     */
    public function draw(MinesweeperGame $game)
    {
        $tile = ImageManagerStatic::make(config('mmg.tile-image-path'));

        $tileSize = config('mmg.tile-size');

        $tile->resize($tileSize, $tileSize);

        /** @var StateGridInterface */
        $grid = $game->grid;

        $width = $grid->getWidth();
        $height = $grid->getHeight();

        $canvas = ImageManagerStatic::canvas($width * $tileSize, $height * $tileSize, array(0, 0, 0, 0));

        $collection = $this->factory->createAssocTileCollection($game->conquered);

        foreach (range(0, $width - 1) as $x) {
            foreach (range(0, $height - 1) as $y) {
                if ($collection->hasTileAt($x, $y)) {
                    $assocTile = $collection->getTileAt($x, $y);

                    $this->drawColoredSquare($canvas, $x, $y, $tileSize, $assocTile->getUser());
                } else {
                    $canvas->insert($tile, 'top-left', $x * $tileSize, $y * $tileSize);
                }
            }
        }

        $path = tempnam(sys_get_temp_dir(), 'image') . '.png';

        $canvas->save($path);

        return $path;
    }

    /**
     * Draw a colored square at.
     *
     * @param Image $canvas
     * @param int $x
     * @param int $y
     * @param int $tileSize
     * @param UserInterface $user
     * @return void
     */
    protected function drawColoredSquare($canvas, $x, $y, $tileSize, UserInterface $user)
    {
        if ( ! isset($this->userColorCache[$user->getId()])) {
            $this->userColorCache[$user->getId()] = ImageUtil::getDominatingColor(ImageManagerStatic::make($user->getAvatar()));
        }

        $color = $this->userColorCache[$user->getId()];

        $canvas->insert(
            ImageManagerStatic::canvas($tileSize - 2, $tileSize - 2, $color),
            'top-left',
            ($x * $tileSize) + 1,
            ($y * $tileSize) + 1
        );
    }
}
