<?php

namespace Taddy\Sdk;

use Taddy\Sdk\Dto\Currency;
use Taddy\Sdk\Dto\CustomEvent;

class Events {

    protected Client $client;
    protected string $pubId;

    public function __construct(string $pubId, Client $client = new Client) {
        $this->client = $client;
        $this->pubId = $pubId;
    }

    public function sendCustomEvent(CustomEvent $event, int $user, ?float $value = null, ?Currency $currency = null, bool $once = false): void {
        $this->client->request('POST', '/events/custom', [
            'pubId' => $this->pubId,
            'event' => $event->value,
            'user' => $user,
            'value' => $value,
            'currency' => $currency?->value,
            'once' => $once
        ]);
    }

}