<?php

declare(strict_types=1);

namespace NodaPay\vendor\nodapay\base\DTO;

use NodaPay\Base\DTO\RequestObject;

class PrepareBuffetRequest extends RequestObject
{
    /**
     * @param string $currency
     * @return self
     */
    public function setCurrency(string $currency): self
    {
        return $this->set('currency', $currency);
    }

    /**
     * @param string $iin
     * @return self
     */
    public function setIin(string $iin): self
    {
        return $this->set('iin', $iin);
    }

    /**
     * @param string $ipAddress
     * @return self
     */
    public function setIpAddress(string $ipAddress): self
    {
        return $this->set('ipAddress', $ipAddress);
    }
}
