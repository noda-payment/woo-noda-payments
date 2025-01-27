<?php

declare(strict_types=1);

namespace NodaPay\Base\DTO;

class ResponseObject extends DynamicDataObject
{
    /**
     * @param int $amount
     * @return self
     */
    public function setStatus(int $amount): self
    {
        return $this->set('status', $amount);
    }

    /**
     * @param $data
     * @return self
     */
    public function setResponse($data): self
    {
        return $this->set('response', $data);
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->get('status');
    }

    public function getResponse()
    {
        return $this->get('response');
    }
}