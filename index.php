<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

return function ($event) {
    return 'Hello ' . ($event['name'] ?? 'world');
};

//wss://hkvyz6wvsi.execute-api.ap-south-1.amazonaws.com/dev