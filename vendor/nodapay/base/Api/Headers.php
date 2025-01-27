<?php

namespace NodaPay\Base\Api;

class Headers
{
    private $headers = [];

    public function __construct($apiKey)
    {
        $this->headers = [
            'Accept' => 'application/json, text/json, text/plain',
            'ChallengeWindowSize' => '',
            'ColorDepth' => '',
            'Content-Type' => 'application/*+json',
            'Height' => '',
            'JavaEnabled' => '',
            'JavaScriptEnabled' => '',
            'Locale' => '',
            'TimezoneOffsetUtcMinutes' => '',
            'User-Agent' => '',
            'Width' => '',
            'x-api-key' => $apiKey
        ];
    }

    public function set($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        return $this->headers[$key] ?? null;
    }

    public function toArray()
    {
        return $this->headers;
    }
}