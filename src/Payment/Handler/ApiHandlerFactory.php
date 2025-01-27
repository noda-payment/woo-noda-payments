<?php

namespace NodaPay\Payment\Handler;

use InvalidArgumentException;
use NodaPay\Api;
use NodaPay\Payment\Contracts\AbstractApiHandlerFactory;
use NodaPay\Payment\Contracts\ApiHandler;

class ApiHandlerFactory implements AbstractApiHandlerFactory
{
    public function getCoreApiHandler(string $apiUrl, string $endpointUrl): ApiHandler
    {
        return new CoreApiHandler($apiUrl, $endpointUrl);
    }

    public function getWPApiHandler(string $apiUrl, string $endpointUrl): ApiHandler
    {
        switch ($endpointUrl) {
            case Api::API_PAYMENT_ENDPOINT:
                $handler = new WPPayUrlApiHandler($apiUrl);
                break;
            case Api::API_BUTTON_ENDPOINT:
                $handler = new WPButtonApiHandler($apiUrl);
                break;
            default:
                throw new InvalidArgumentException('Endpoint '.$endpointUrl.' is not supported by noda pay plugin');
        }

        return $handler;
    }
}
