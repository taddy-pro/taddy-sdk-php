<?php

namespace Taddy\Sdk;

use Psr\Log\LoggerInterface;
use Taddy\Sdk\Dto\ExchangeFeedItem;
use Taddy\Sdk\Dto\User;
use Throwable;

class Exchange {

    protected Taddy $taddy;
    protected LoggerInterface $logger;

    public function __construct(Taddy $taddy) {
        $this->taddy = $taddy;
        $this->logger = $taddy->getOptions()->getLogger();
    }

    /**
     * @param User $user
     * @param int $limit
     * @param string $imageFormat
     * @param bool $autoImpressions
     * @return ExchangeFeedItem[]
     */
    public function getFeed(User $user, int $limit = 4, string $imageFormat = 'webp', bool $autoImpressions = false): array {
        $this->logger->debug('Getting exchange feed...');
        try {
            $data = $this->taddy->request('POST', '/v1/exchange/feed', [
                'pubId' => $this->taddy->getPubId(),
                'user' => $this->taddy->toArray($user),
                'origin' => 'server',
                'limit' => $limit,
                'imageFormat' => $imageFormat,
                'autoImpressions' => $autoImpressions,
            ]);
            $this->logger->debug('Feed', $data);
            return $this->taddy->toObjects($data, ExchangeFeedItem::class);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
    }


    public function impressions(User $user, array $items): void {
        try {
            $this->logger->debug('Sending impressions event...');
            $items = array_map(fn(int|string|ExchangeFeedItem $item) => is_scalar($item) ? $item : $item->id, $items);
            $this->taddy->request('POST', '/v1/exchange/impressions', [
                'pubId' => $this->taddy->getPubId(),
                'user' => $this->taddy->toArray($user),
                'origin' => 'server',
                'ids' => $items,
            ]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function check(User $user, ExchangeFeedItem|int|string $item): bool {
        try {
            $this->logger->debug('Check exchange...');
            $result = (bool)$this->taddy->request('POST', '/v1/exchange/check', [
                'pubId' => $this->taddy->getPubId(),
                'user' => $this->taddy->toArray($user),
                'origin' => 'server',
                'exchangeId' => is_scalar($item) ? $item : $item->id,
            ]);
            $this->logger->debug('Result: ' . ($result ? 'SUCCESS' : 'PENDING'));
            return $result;
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

}