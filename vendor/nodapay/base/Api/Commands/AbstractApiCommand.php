<?php

declare(strict_types=1);

namespace NodaPay\Base\Api\Commands;

use GuzzleHttp\Client;
use NodaPay\Base\DTO\RequestObject;
use NodaPay\Base\DTO\ResponseObject;
use NodaPay\Base\DTO\DynamicDataObject;
use NodaPay\Base\Api\Headers;

abstract class AbstractApiCommand
{
    public const LIVE_URL = 'https://api.noda.live/';

    public const SANDBOX_ULR = 'https://api.stage.noda.live/';

    protected $guzzleClient;
    protected $method;
    protected $endpoint;
    protected $headers;

    public function __construct(Client $guzzleClient, $config)
    {
        $this->guzzleClient = $guzzleClient;
        $this->initConfig($config);
    }

    public function initConfig($config)
    {
        if (!isset($config['api_key'])) {
            throw new \Exception('API key required');
        }

        $this->initHeaders($config['api_key']);
    }

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function initHeaders($apiKey)
    {
        $this->headers = new Headers($apiKey);
    }

    abstract protected function getResponseInstance($data);

    public function process($request)
    {
        $endpoint = $this->getEndpoint();

        if ($this->method === 'GET') {
            $result = $this->guzzleClient->get($endpoint, [
                'headers' => $this->headers->toArray(),
                'query' => $request->toArray()
            ]);
        } elseif ($this->method === 'POST' || $this->method === 'PUT') {
            $result = $this->guzzleClient->post(
                $endpoint,
                [
                    'headers' => $this->headers->toArray(),
                    'json' => $request->toArray()
                ]
            );
        }

        if (!isset($result)) {
            return null;
        }

        $response = new ResponseObject();
        $response->setStatus($result->getStatusCode())
            ->setResponse($this->sanitizeResponse($result));

        return $response;
    }

    private function sanitizeResponse($response)
    {
        $data = json_decode($response->getBody()->getContents(), true);

       if (get_class($this) == 'NodaPay\Base\Api\Commands\PrepareBuffet') {
           return $data;
       } else {
           return $this->getResponseInstance($data);
       }
    }
}