<?php

declare(strict_types=1);

namespace NodaPay\Base\Api\Commands;

use NodaPay\Base\DTO\DynamicDataObject;
use NodaPay\Base\DTO\PreparePayButtonRequest;
use NodaPay\vendor\nodapay\base\DTO\PrepareBuffetRequest;

class PrepareBuffet extends AbstractApiCommand
{
    protected $method = 'GET';

    protected function getEndpoint(): string
    {
        return 'api/providers/top';
    }

    protected function getResponseInstance($data): DynamicDataObject
    {
        return new PrepareBuffetRequest($data);
    }
}