<?php

declare(strict_types=1);

namespace NodaPay\Base\Api\Commands;

use NodaPay\Base\DTO\PreparePayButtonRequest;
use NodaPay\Base\DTO\ResponseObject;

interface PreparePayButtonInterface
{
    public function process(PreparePayButtonRequest $request);
}