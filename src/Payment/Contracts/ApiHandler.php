<?php

namespace NodaPay\Payment\Contracts;

use NodaPay\Payment\Abstracts\GenericResponse;

interface ApiHandler
{
    public function handle(
        string $url,
        array $body,
        array $header
    ): GenericResponse;
}
