<?php

declare(strict_types=1);

namespace NodaPay\Base\DTO;

class CreatePaymentResponse extends DynamicDataObject
{
    /**
     * @param string $id
     * @return self
     */
    public function setId(string $id): self
    {
        return $this->set('id', $id);
    }

    /**
     * @param string $url
     * @return self
     */
    public function setUrl(string $url): self
    {
        return $this->set('url', $url);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->get('id');
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->get('url');
    }
}