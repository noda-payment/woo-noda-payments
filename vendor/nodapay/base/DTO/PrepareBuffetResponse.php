<?php

declare(strict_types=1);

namespace NodaPay\vendor\nodapay\base\DTO;

use NodaPay\Base\DTO\DynamicDataObject;

class PrepareBuffetResponse extends DynamicDataObject
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
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        return $this->set('type', $type);
    }

    /**
     * @param string $displayName
     * @return self
     */
    public function setDisplayName(string $displayName): self
    {
        return $this->set('type', $displayName);
    }

    /**
     * @param string $country
     * @return self
     */
    public function setCountry(string $country): self
    {
        return $this->set('type', $country);
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

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->get('id');
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->get('url');
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->get('url');
    }
}