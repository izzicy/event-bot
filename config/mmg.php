<?php

return [
    'default-channel' => env('MULTIPLAYER_MINESWEEPER_DEFAULT_CHANNEL'),
    'test-channel' => env('MULTIPLAYER_MINESWEEPER_TEST_CHANNEL'),

    'ui-in-row' => 4,

    /**
     * The penalty upon a wrong move.
     */

    'move-penalty' => 1,

    /**
     * The penalty upon a wrong move.
     */

    'score-penalty' => 5,

    /**
     * The defualt number of user moves.
     */

    'default-user-moves' => 5,

    /**
     * Image paths.
     */

    'tile-image-path' => storage_path('app/mmg/out-tile.png'),
    'mine-image-path' => storage_path('app/mmg/mine.png'),
    'flag-image-path' => storage_path('app/mmg/flag.png'),
    'ui-image-path' => storage_path('app/mmg/ui.png'),

    /**
     * Default font path.
     */

    'font-path' => storage_path('app/mmg/font.ttf'),

    /**
     * Default file size.
     */

    'tile-size' => 64,
];
