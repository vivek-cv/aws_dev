<?php

namespace App;

use Bref\Context\Context;
use Bref\Event\Sns\SnsEvent;
use Bref\Event\Sns\SnsHandler;
use App\WebsocketHandler;

class SnsTopicHandler extends SnsHandler
{
    public function handleSns(SnsEvent $event, Context $context): void
    {
        foreach ($event->getRecords() as $record) {
            $message = json_decode($record->getMessage(), true);

            (new WebsocketHandler())->broadcastMessage($message['message'], $message['event'], $message['for']);
        }
    }
}
