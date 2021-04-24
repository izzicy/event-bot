<?php

namespace App\Mmg\Draw;

use App\Mmg\Contracts\DrawInterface;
use App\Mmg\Contracts\FactoryInterface;
use App\Mmg\Testers\UserFlagTester;
use App\Mmg\Testers\UserMovesTester;
use App\Mmg\Testers\UserScoreTester;
use App\Services\Users\UserInterface;
use App\Util\Intervention\ImageUtil;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

class UiDrawer implements DrawInterface
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var Image */
    protected $ui;

    /**
     * Drawer constructor.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
        $this->ui = ImageManagerStatic::make(config('mmg.ui-image-path'));
    }


    /** @inheritDoc */
    public function draw($game)
    {
        $userMovesTester = new UserMovesTester;
        $userFlagTester = new UserFlagTester;
        $userScoreTester = new UserScoreTester;

        $tester = $this->factory->createAggregateTester([
            $userMovesTester,
            $userFlagTester,
            $userScoreTester,
        ]);

        foreach ($game->tiles as $tile) {
            $tester->testTile($tile);
        }

        $users = $userScoreTester->getUsers();
        $userCount = count($users);
        $columns = config('mmg.ui-in-row');
        $rows = floor($userCount / $columns) + 1;
        $width = $this->ui->getWidth();
        $height = $this->ui->getHeight();
        $canvas = ImageManagerStatic::canvas($width * $columns, $height * $rows);

        foreach ($users as $key => $user) {
            $ui = $this->createUi($user, $userScoreTester->getScoreCount($user), $userFlagTester->getFlagCount($user), $userMovesTester->getNumberOfMoves($user));
            $x = $key % $columns;
            $y = floor($key / $columns);
            $canvas->insert($ui, 'top-left', $x * $width, $y * $height);
        }

        return $canvas->getCore();
    }

    /**
     * Create a ui for the given user.
     *
     * @param UserInterface $user
     * @param int $score
     * @param int $flagCount
     * @param int $moveCount
     * @return Image
     */
    protected function createUi($user, $score, $flagCount, $moveCount)
    {
        $ui = ImageManagerStatic::canvas($this->ui->getWidth(), $this->ui->getHeight(), 'rgba(0, 0, 0, 0)');

        $profile = ImageManagerStatic::make($user->getAvatar());
        $profile->resize(60, 60);

        $ui->insert($profile, 'top-left', 21, 12);

        $ui->insert($this->ui, 'top-left', 0, 0);

        $ui->text($user->getUsername(), 137, 45, function($font) {
            $font->file(config('mmg.font-path'));
            $font->size(13);
            $font->color('#111111');
            $font->align('center');
            $font->valign('middle');
        });

        $ui->text($flagCount, 118, 114, function($font) {
            $font->file(config('mmg.font-path'));
            $font->size(13);
            $font->color('#111111');
            $font->align('center');
            $font->valign('middle');
        });

        $ui->text($moveCount, 118, 158, function($font) {
            $font->file(config('mmg.font-path'));
            $font->size(13);
            $font->color('#111111');
            $font->align('center');
            $font->valign('middle');
        });

        $ui->text($score, 118, 211, function($font) {
            $font->file(config('mmg.font-path'));
            $font->size(13);
            $font->color('#111111');
            $font->align('center');
            $font->valign('middle');
        });

        $dominatingColour = ImageUtil::getDominatingColor(ImageManagerStatic::make($user->getAvatar()));

        $ui->insert(ImageManagerStatic::canvas(157, 32, $dominatingColour), 'top-left', 21, 249);

        return $ui;
    }
}
