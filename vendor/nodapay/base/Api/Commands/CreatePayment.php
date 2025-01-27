<?php

declare(strict_types=1);

namespace NodaPay\Base\Api\Commands;

use NodaPay\Base\DTO\DynamicDataObject;
use NodaPay\Base\DTO\CreatePaymentResponse;

class CreatePayment extends AbstractApiCommand implements CreatePaymentInterface
{
    protected $method = 'POST';

    protected function getEndpoint(): string
    {
        return 'api/payments';
    }

    protected function getResponseInstance($data): DynamicDataObject
    {
        return new CreatePaymentResponse($data);
    }
}