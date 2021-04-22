<?php

return [
    'default-channel' => env('MULTIPLAYER_MINESWEEPER_DEFAULT_CHANNEL'),
    'test-channel' => env('MULTIPLAYER_MINESWEEPER_TEST_CHANNEL'),

    'tile-size' => 64,

    'tile-image-path' => storage_path('app/mmg/out-tile.png'),
    'mine-image-path' => storage_path('app/mmg/mine.png'),
    'flag-image-path' => storage_path('app/mmg/flag.png'),
    'font-path' => storage_path('app/mmg/font.ttf'),
];
