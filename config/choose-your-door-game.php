<?php

return [
    'default-channel' => env('CHOOSE_YOUR_DOOR_DEFAULT_CHANNEL'),
    'test-channel' => env('CHOOSE_YOUR_DOOR_TEST_CHANNEL'),

    'phrases-file' => storage_path('app/choose-your-door/phrases.json'),

    'win-emojis' => [
        storage_path('app/choose-your-door/emoji/sunglasses.png'),
        storage_path('app/choose-your-door/emoji/laughing.png'),
    ],

    'lose-emojis' => [
        storage_path('app/choose-your-door/emoji/angry.png'),
        storage_path('app/choose-your-door/emoji/crying.png'),
        storage_path('app/choose-your-door/emoji/scream.png'),
    ],

    'vote-emojis' => [
        'door-1' => "1️⃣",
        'door-2' => "2️⃣",
        'door-3' => "3️⃣",
        'door-4' => "4️⃣",
        'door-5' => "5️⃣",
        'door-6' => "6️⃣",
        'door-7' => "7️⃣",
        'door-8' => "8️⃣",
        'door-9' => "9️⃣",
        'door-10' => "🔟",
        'door-11' => "🇭",
    ],
];
