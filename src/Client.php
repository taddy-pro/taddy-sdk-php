<?php

namespace Taddy\Sdk;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Taddy\Sdk\Utils\EnumNormalizer;

class Client {

    protected HttpClient $client;
    protected Serializer $serializer;
    protected LoggerInterface $logger;

    public function __construct(string $baseUrl = 'https://api.taddy.pro') {
        $this->client = new HttpClient([
            'base_uri' => $baseUrl,
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
        $this->logger = new NullLogger();
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
        $this->logger->debug('Requesting ' . $method . ' ' . $path, $params);
        $options = [];
        if ($method === 'GET') {
            $options[RequestOptions::QUERY] = ['__payload' => json_encode($params)];
        } else {
            $options[RequestOptions::JSON] = $params;
        }
        try {
            $res = $this->client->request($method, $path, $options);
            $code = $res->getStatusCode();
            $body = $res->getBody()->getContents();
            print_r($body);
            $json = json_validate($body) ? json_decode($body, true) : null;
            $this->logger->debug('Response (' . $code. '): ' . $body);
            if ($code >= 200 && $code < 300) {
                return $json['result'] ?? null;
            } elseif ($error = $json['error'] ?? false) {
                $this->logger->error('Error: ' . $error);
                throw new Exception('Taddy: ' . $error);
            } else {
                $this->logger->error('Unexpected error');
                throw new Exception('Taddy: Unexpected error ' . $code . ': ' . substr($body, 0, 100));
            }
        } catch (ConnectException $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }
    }

    public function getLogger(): LoggerInterface {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }

}