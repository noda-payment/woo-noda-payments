<?php

declare(strict_types=1);

namespace NodaPay\Base\Api\Commands;

use NodaPay\Base\DTO\DynamicDataObject;
use NodaPay\Base\DTO\PreparePayButtonRequest;

class PreparePayButton extends AbstractApiCommand implements PreparePayButtonInterface
{
    protected $method = 'POST';

    protected function getEndpoint(): string
    {
        return 'api/payments/logo';
    }

    protected function getResponseInstance($data): DynamicDataObject
    {
        return new PreparePayButtonRequest($data);
    }
}