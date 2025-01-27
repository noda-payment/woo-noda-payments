<?php

declare(strict_types=1);

namespace NodaPay\Base\DTO;

class CreatePaymentRequest extends RequestObject
{
    /**
     * @param float $amount
     * @return self
     */
    public function setAmount(float $amount): self
    {
        return $this->set('amount', $amount);
    }

    /**
     * @param string $currency
     * @return self
     */
    public function setCurrency(string $currency): self
    {
        return $this->set('currency', $currency);
    }

    /**
     * @param string $customerId
     * @return self
     */
    public function setCustomerId(string $customerId): self
    {
        return $this->set('customerId', $customerId);
    }

    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        return $this->set('description', $description);
    }

    /**
     * @param string $shopId
     * @return self
     */
    public function setShopId(string $shopId): self
    {
        return $this->set('shopId', $shopId);
    }

    /**
     * @param string $paymentId
     * @return self
     */
    public function setPaymentId(string $paymentId): self
    {
        return $this->set('paymentId', $paymentId);
    }

    /**
     * @param string $returnUrl
     * @return self
     */
    public function setReturnUrl(string $returnUrl): self
    {
        return $this->set('returnUrl', $returnUrl);
    }

    /**
     * @param string $webhookUrl
     * @return self
     */
    public function setWebhookUrl(string $webhookUrl): self
    {
        return $this->set('webhookUrl', $webhookUrl);
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

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        return $this->set('ipAddress', $email);
    }

    /**
     * @param string $providerId
     * @return self
     */
    public function setProviderId(string $providerId): self
    {
        return $this->set('ipAddress', $providerId);
    }
}