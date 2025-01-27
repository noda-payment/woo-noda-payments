<?php

namespace NodaPay\Payment\Responses;

use NodaPay\Payment\Abstracts\GenericResponse;

class ErrorResponse implements GenericResponse
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $message;


    /**
     * ErrorResponse constructor.
     * @param int $statusCode
     * @param string $message
     */
    public function __construct(int $statusCode, string $message)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
