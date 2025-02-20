<?php

namespace Taddy\Sdk;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Taddy\Sdk\Dto\Currency;
use Taddy\Sdk\Dto\CustomEvent;
use Taddy\Sdk\Dto\ExchangeFeedItem;
use Taddy\Sdk\Dto\User;
use Throwable;

class Exchange {

    protected string $pubId;
    protected Client $client;
    protected LoggerInterface $logger;

    public function __construct(string $pubId, Client $client = new Client, LoggerInterface $logger = new NullLogger) {
        $this->pubId = $pubId;
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @param int $limit
     * @param bool $autoImpressions
     * @return ExchangeFeedItem[]
     */
    public function getFeed(User $user, int $limit = 4, bool $autoImpressions = false): array {
        $this->logger->debug('Getting exchange feed...');
        try {
            $data = $this->client->request('POST', '/exchange/feed', [
                'pubId' => $this->pubId,
                'limit' => $limit,
                'user' => $this->client->toArray($user),
                'autoImpressions' => $autoImpressions,
                'origin' => 'server',
            ]);
            $this->logger->debug('Feed', $data);
            return $this->client->toObjects($data, ExchangeFeedItem::class);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
    }

    public function customEvent(CustomEvent $event, User|int $user, ?float $value = null, ?Currency $currency = null, bool $once = false): void {
        try {
            $this->logger->debug('Sending custom event...');
            $this->client->request('POST', '/events/custom', [
                'pubId' => $this->pubId,
                'event' => $event->value,
                'user' => is_int($user) ? $user : $user->id,
                'value' => $value,
                'currency' => $currency?->value,
                'once' => $once,
                'origin' => 'server',
            ]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function impressionsEvent(User|int $user, array $items): void {
        try {
            $this->logger->debug('Sending impressions event...');
            $items = array_map(fn(int|ExchangeFeedItem $item) => is_int($item) ? $item : $item->id, $items);
            $this->client->request('POST', '/events/impressions', [
                'pubId' => $this->pubId,
                'user' => is_int($user) ? $user : $user->id,
                'ids' => $items,
                'origin' => 'server',
            ]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function startEvent(User $user, ?string $start = null): void {
        try {
            $this->logger->debug('Sending start event...');
            if(isset($start)) $start = trim(str_replace('/start', '', $start));
            $this->client->request('POST', '/events/start', [
                'pubId' => $this->pubId,
                'user' => $this->client->toArray($user),
                'start' => $start,
                'origin' => 'server',
            ]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function check(User|int $user, ExchangeFeedItem|int $item): bool {
        try {
            $this->logger->debug('Check exchange...');
            $result = (bool)$this->client->request('POST', '/exchange/check', [
                'pubId' => $this->pubId,
                'userId' => is_int($user) ? $user : $user->id,
                'exchangeId' => is_int($item) ? $item : $item->id,
                'origin' => 'server',
            ]);
            $this->logger->debug('Result: ' . ($result ? 'SUCCESS' : 'PENDING'));
            return $result;
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

}