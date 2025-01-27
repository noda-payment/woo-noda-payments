<?php

namespace NodaPay;

use NodaPay\Payment\Contracts\ApiClient;

class CoreApiClient implements ApiClient
{
    /**
     * @var string
     */
    private $apiUrl;

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function get(
        string $url,
        array $header,
        bool $return_transfer = true,
        string $encoding = '',
        int $max_redirects = 3,
        int $max_timeout = 10,
        string $http_version = '1.1'
    ) {
        // TODO: Implement get() method. This will be implemented and used after we add a core payment plugin
    }

    public function post(
        string $url,
        array $body,
        array $header,
        bool $return_transfer = true,
        string $encoding = '',
        int $max_redirects = 3,
        int $max_timeout = 10,
        string $http_version = '1.1'
    ) {
        // TODO: Implement post() method. This will be implemented and used after we add a core payment plugin
    }
}
