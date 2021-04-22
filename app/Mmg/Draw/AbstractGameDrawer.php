<?php

namespace App\Mmg\Draw;

use App\Mmg\Contracts\DrawInterface;
use App\Mmg\Contracts\FactoryInterface;
use App\Services\Users\UserInterface;
use App\Util\Intervention\ColorizeFromImageFilter;
use App\Util\Intervention\ImageUtil;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

abstract class AbstractGameDrawer implements DrawInterface
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var Image */
    protected $tile;

    /** @var Image */
    protected $flag;

    /** @var Image */
    protected $mine;

    /** @var Image */
    protected $canvas;

    /** @var array */
    protected $userColorCache = [];

    /** @var array */
    protected $flagCache = [];

    /**
     * Drawer constructor.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
        $this->tile = ImageManagerStatic::make(config('mmg.tile-image-path'));
        $this->flag = ImageManagerStatic::make(config('mmg.flag-image-path'));
        $this->mine = ImageManagerStatic::make(config('mmg.mine-image-path'));

        $tileSize = $this->getTileSize();

        $this->tile->resize($tileSize, $tileSize);
        $this->mine->resize($tileSize, $tileSize);
        $this->canvas = ImageManagerStatic::canvas(1, 1);
    }

    /**
     * Create a new image canvas.
     *
     * @param int $width
     * @param int $height
     * @return Image
     */
    public function createCanvas($width, $height)
    {
        $tileSize = $this->getTileSize();

        return $this->canvas = ImageManagerStatic::canvas($width * $tileSize, $height * $tileSize, array(0, 0, 0, 0));
    }

    /**
     * Get the tile size.
     *
     * @return int
     */
    protected function getTileSize()
    {
        return config('mmg.tile-size');
    }

    /**
     * Draw a colored square at.
     *
     * @param int $x
     * @param int $y
     * @param UserInterface $user
     * @return void
     */
    protected function drawColoredSquareAt($x, $y, UserInterface $user)
    {
        $tileSize = $this->getTileSize();
        $canvas = $this->canvas;

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

    /**
     * Draw a colored square at.
     *
     * @param int $x
     * @param int $y
     * @param UserInterface[] $flaggers
     * @return void
     */
    protected function drawFlaggersAt($x, $y, $flaggers)
    {
        $tileSize = $this->getTileSize();
        $canvas   = $this->canvas;
        $columns  = 4;
        $size     = round($this->getTileSize() / $columns + 1);
        $padding  = round($this->getTileSize() / ($columns + 1) / $columns);


        foreach ($flaggers as $key => $flagger) {
            if ( ! isset($this->flagCache[$flagger->getId()])) {
                $flag = clone $this->flag;

                $colorizer = app(ColorizeFromImageFilter::class, [
                    'image' => ImageManagerStatic::make($flagger->getAvatar()),
                    'strength' => 60,
                ]);

                $flag = $colorizer->applyFilter($flag);
                $flag->resize($size, $size);

                $this->flagCache[$flagger->getId()] = $flag;
            }

            $flag = $this->flagCache[$flagger->getId()];

            $offsetX = ($key % $columns) * ($size + $padding) + $padding;
            $offsetY = floor($key / $columns) * ($size + $padding) + $padding;

            $canvas->insert(
                $flag,
                'top-left',
                ($x * $tileSize) + $offsetX,
                ($y * $tileSize) + $offsetY
            );
        }
    }

    /**
     * Draw a black square at.
     *
     * @param int $x
     * @param int $y
     * @return void
     */
    protected function drawBlackSquareAt($x, $y)
    {
        $tileSize = $this->getTileSize();

        $this->canvas->insert(
            ImageManagerStatic::canvas($tileSize - 2, $tileSize - 2, '#000000'),
            'top-left',
            ($x * $tileSize) + 1,
            ($y * $tileSize) + 1
        );
    }

    /**
     * Draw an empty tile.
     *
     * @param int $x
     * @param int $y
     * @return void
     */
    protected function drawEmptyTileAt($x, $y)
    {
        $tile = $this->tile;
        $canvas = $this->canvas;
        $tileSize = $this->getTileSize();

        $canvas->insert($tile, 'top-left', $x * $tileSize, $y * $tileSize);
        $this->drawCoordsAt($x, $y);
    }

    /**
     * Draw a mine at.
     *
     * @param int $x
     * @param int $y
     * @return void
     */
    protected function drawMineAt($x, $y)
    {
        $mine = $this->mine;
        $canvas = $this->canvas;
        $tileSize = $this->getTileSize();

        $canvas->insert($mine, 'top-left', $x * $tileSize, $y * $tileSize);
        $this->drawCoordsAt($x, $y);
    }

    /**
     * Draw a count at the given position.
     *
     * @param int $x
     * @param int $y
     * @param int $count
     * @return void
     */
    protected function drawCountAt($x, $y, $count)
    {
        $tileSize = $this->getTileSize();

        $this->canvas->text($count, round($x * $tileSize + $tileSize * 0.5), round($y * $tileSize + $tileSize * 0.5), function($font) {
            $font->file(config('mmg.font-path'));
            $font->size(13);
            $font->color('#fffff');
            $font->align('center');
            $font->valign('middle');
        });
    }

    /**
     * Draw the coords at.
     *
     * @param int $x
     * @param int $y
     * @return void
     */
    protected function drawCoordsAt($x, $y)
    {
        $canvas = $this->canvas;
        $tileSize = $this->getTileSize();

        $canvas->text($x, round($x * $tileSize + $tileSize * 0.25), round($y * $tileSize + $tileSize * 0.25), function($font) {
            $font->file(config('mmg.font-path'));
            $font->size(13);
            $font->color('#fffff');
            $font->align('center');
            $font->valign('top');
        });
        $canvas->text($y, round($x * $tileSize + $tileSize * 0.75), round($y * $tileSize + $tileSize * 0.75), function($font) {
            $font->file(config('mmg.font-path'));
            $font->size(13);
            $font->color('#fffff');
            $font->align('center');
            $font->valign('bottom');
        });
    }

    /**
     * Check if nearby has been conquered.
     *
     * @param int $x
     * @param int $y
     * @param StateGridInterface $grid
     * @param UserAssocTilesCollectionInterface $collection
     * @return bool
     */
    protected function nearbyHasBeenConquered($x, $y, $grid, $collection)
    {
        $candidates = [
            [$x - 1, $y],
            [$x, $y - 1],
            [$x - 1, $y - 1],
            [$x + 1, $y],
            [$x, $y + 1],
            [$x + 1, $y + 1],
            [$x - 1, $y + 1],
            [$x + 1, $y - 1],
        ];

        foreach ($candidates as $candidate) {
            if ($grid->getStateAt($candidate[0], $candidate[1]) === 'empty' && $collection->hasTileAt($candidate[0], $candidate[1])) {
                return true;
            }
        }

        return false;
    }

    /** @inheritDoc */
    abstract public function draw($game);
}
