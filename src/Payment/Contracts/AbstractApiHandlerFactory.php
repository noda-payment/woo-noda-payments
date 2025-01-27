<?php

namespace NodaPay\Payment\Contracts;

interface AbstractApiHandlerFactory
{
    public function getCoreApiHandler(string $apiUrl, string $endpointUrl): ApiHandler;

    public function getWPApiHandler(string $apiUrl, string $endpointUrl): ApiHandler;
}
