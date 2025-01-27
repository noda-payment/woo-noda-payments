<?php

namespace NodaPay\Payment\Handler;

use NodaPay\Payment\Abstracts\GenericResponse;
use NodaPay\Payment\Contracts\ApiHandler;

class CoreApiHandler implements ApiHandler
{
    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $endpoint;

    public function __construct(string $apiUrl, string $endpointUrl)
    {
        $this->apiUrl = $apiUrl;
        $this->endpoint = $endpointUrl;
    }

    public function handle(string $url, array $body, array $header): GenericResponse
    {
        // TODO: Implement handle() method.
    }
}
