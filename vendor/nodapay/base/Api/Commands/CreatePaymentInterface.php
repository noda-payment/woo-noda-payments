<?php

declare(strict_types=1);

namespace NodaPay\Base\Api\Commands;

use NodaPay\Base\DTO\RequestObject;
use NodaPay\Base\DTO\ResponseObject;

interface CreatePaymentInterface
{
    public function process(RequestObject $request);
}