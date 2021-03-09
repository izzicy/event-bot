<?php

return [
    'default-channel' => env('MULTIPLAYER_MINESWEEPER_DEFAULT_CHANNEL'),
    'test-channel' => env('MULTIPLAYER_MINESWEEPER_TEST_CHANNEL'),

    'tile-size' => 32,

    'tile-image-path' => storage_path('app/mmg/out-tile.png'),
];
