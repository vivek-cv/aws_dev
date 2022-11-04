<?php declare(strict_types=1);

namespace App;

use Bref\Context\Context;
use Bref\Event\ApiGateway\WebsocketEvent;
use Bref\Event\Http\HttpResponse;
use Bref\Websocket\SimpleWebsocketClient;

class WebsocketHandler extends \Bref\Event\ApiGateway\WebsocketHandler
{
    private ConnectionStorage $connectionStorage;

    public function __construct()
    {
        $this->connectionStorage = new ConnectionStorage;
    }

    public function handleWebsocket(WebsocketEvent $event, Context $context): HttpResponse
    {
        switch ($event->getEventType()) {
            case 'CONNECT':
                // $body = json_decode($event->getBody(), true);

                $body = ['event_type' => 'meeting_status', 'for' => 'client_6.123'];
                $this->connectionStorage->storeNewConnection($event->getConnectionId(), $body);
                return new HttpResponse('connect');

            case 'DISCONNECT':
                $this->connectionStorage->removeConnection($event->getConnectionId());
                return new HttpResponse('disconnect');

            default:
                if ($event->getBody() === 'status_update') {
                    $this->broadcastMessage('status updated');
                }

                return new HttpResponse('');
        }
    }

    public function broadcastMessage($message, $event = 'status_update', $for = '123'): void
    {
        $websocketClient = SimpleWebsocketClient::create(
            apiId: getenv('API_GATEWAY_ID'),
            region: getenv('AWS_REGION'),
            stage: 'dev',
        );

        foreach ($this->connectionStorage->getAllConnections($event, $for) as $connectionId) {
            $websocketClient->message($connectionId, $message);
        }
    }
}