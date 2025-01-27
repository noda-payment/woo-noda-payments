<?php

declare(strict_types=1);

namespace NodaPay\Base\Api;

use NodaPay\Base\Api\Commands\AbstractApiCommand;
use GuzzleHttp\Client;

class CommandFactory
{
    public static function create(string $type, array $config = [])
    {
        if (isset($config['test_mode']) && $config['test_mode']) {
            $baseUrl = AbstractApiCommand::SANDBOX_ULR;
        } else {
            $baseUrl = AbstractApiCommand::LIVE_URL;
        }

        if (!isset($config['api_key'])) {
            throw new \Exception('API key missing.');
        }

        $guzzleClient = new Client((['base_uri' => $baseUrl]));

        return new $type($guzzleClient, $config);
    }
}