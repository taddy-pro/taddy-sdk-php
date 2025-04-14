<?php

namespace Taddy\Sdk;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Taddy\Sdk\Dto\Currency;
use Taddy\Sdk\Dto\CustomEvent;
use Taddy\Sdk\Dto\User;
use Taddy\Sdk\Utils\EnumNormalizer;
use Taddy\Sdk\Utils\TaddyOptions;
use Throwable;

class Taddy {

    protected string $pubId;
    protected HttpClient $client;
    protected Serializer $serializer;
    protected TaddyOptions $options;

    public function __construct(string $pubId, ?TaddyOptions $options = null) {
        $this->pubId = $pubId;
        $options ??= new TaddyOptions('https://api.taddy.pro');
        $this->options = $options;
        $this->client = new HttpClient([
            'base_uri' => $this->options->getApiUrl(),
            RequestOptions::CONNECT_TIMEOUT => 3,
            RequestOptions::TIMEOUT => 5,
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
        $encoders = [new JsonEncoder];
        $normalizers = [new EnumNormalizer, new DateTimeNormalizer, new ObjectNormalizer];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function ads(): Ads {
        static $ads;
        return $ads ?? $ads = new Ads($this);
    }

    public function exchange(): Exchange {
        static $exchange;
        return $exchange ?? $exchange = new Exchange($this);
    }

    public function start(User $user, string $start): void {
        try {
            $this->options->getLogger()->debug('Sending start event...');
            $start = trim(str_replace('/start', '', $start));
            $this->request('POST', '/v1/events/start', [
                'pubId' => $this->pubId,
                'user' => $this->toArray($user),
                'origin' => 'server',
                'start' => $start,
            ]);
        } catch (Throwable $e) {
            $this->options->getLogger()->error($e->getMessage());
        }
    }

    public function customEvent(User $user, CustomEvent $event, ?float $value = null, Currency|string|null $currency = null, bool $once = false): void {
        try {
            $this->options->getLogger()->debug('Sending custom event...');
            $this->request('POST', '/v1/events/custom', $this->toArray([
                'pubId' => $this->pubId,
                'user' => $user,
                'origin' => 'server',
                'event' => $event->value,
                'value' => $value,
                'currency' => $currency,
                'once' => $once,
            ]));
        } catch (Throwable $e) {
            $this->options->getLogger()->error($e->getMessage());
        }
    }

    public function toArray(mixed $data): array {
        return $this->serializer->normalize($data);
    }

    public function toObject(array $data, string $class): object {
        return $this->serializer->denormalize($data, $class);
    }

    public function toObjects(array $array, string $class): array {
        return array_map(fn(array $data) => $this->toObject($data, $class), $array);
    }

    public function request(string $method, string $path, array $params = []): mixed {
        $this->options->getLogger()->debug('Requesting ' . $method . ' ' . $path, $params);
        $options = [];
        if ($method === 'GET') {
            $options[RequestOptions::QUERY] = ['__payload' => json_encode($params)];
        } else {
            $params = $this->toArray($params);
            $options[RequestOptions::JSON] = $params;
        }
        try {
            $res = $this->client->request($method, $path, $options);
            $code = $res->getStatusCode();
            $body = $res->getBody()->getContents();
            $json = json_decode($body, true);
            if(json_last_error()) $json = null;
            $this->options->getLogger()->debug('Response (' . $code. '): ' . $body);
            if ($code >= 200 && $code < 300) {
                return $json['result'] ?? null;
            } elseif ($error = $json['error'] ?? false) {
                $this->options->getLogger()->error('Error: ' . $error);
                throw new Exception('Taddy: ' . $error);
            } else {
                $this->options->getLogger()->error('Unexpected error');
                throw new Exception('Taddy: Unexpected error ' . $code . ': ' . substr($body, 0, 100));
            }
        } catch (ConnectException $e) {
            $this->options->getLogger()->critical($e->getMessage());
            throw $e;
        }
    }

    public function getOptions(): TaddyOptions {
        return $this->options;
    }

    public function getPubId(): string {
        return $this->pubId;
    }

}