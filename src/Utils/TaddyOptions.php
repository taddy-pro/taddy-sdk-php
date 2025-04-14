<?php

namespace Taddy\Sdk\Utils;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TaddyOptions {

    protected bool $debug;
    protected string $apiUrl;
    protected string $botApiUrl;
    protected ?string $token;
    protected LoggerInterface|NullLogger $logger;

    public function __construct(string $apiUrl, ?string $botApiUrl = null, ?string $token = null, bool $debug = false, LoggerInterface $logger = null) {
        $this->apiUrl = $apiUrl;
        $this->token = $token;
        $this->debug = $debug;
        $this->logger = $logger ?? new NullLogger();
        $this->botApiUrl = $botApiUrl ?? 'https://api.telegram.org';
    }

    public function getApiUrl(): string {
        return $this->apiUrl;
    }

    public function isDebug(): bool {
        return $this->debug;
    }

    public function getToken(): ?string {
        return $this->token;
    }

    public function getLogger(): NullLogger|LoggerInterface {
        return $this->logger;
    }

    public function getBotApiUrl(): string {
        return $this->botApiUrl;
    }

}