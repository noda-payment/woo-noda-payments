<?php

namespace NodaPay\Traits;

use NodaPay\Gateway;
use NodaPay\Payment\NodaPayment;
use WC_Order;

trait NodaSettings
{
    public static $paymentRedirectOptionShop = 'shop';
    public static $paymentRedirectOptionCart = 'cart';
    public static $paymentRedirectOptionHome = 'home';
    public static $paymentRedirectOptionOrdersList = 'orders';
    public static $paymentRedirectOptionCurrentOrder = 'current_order';

    private $wooCommerceSettingsPrefix = 'woocommerce_';
    private $settingsSuffix = '_settings';

    public function getOptions(): array
    {
        return get_option($this->getSettingsKey(), []);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getOption(string $key)
    {
        $options = $this->getOptions();

        if (!isset($options[$key])) {
            return null;
        }

        return $options[$key];
    }

    private function getSettingsKey(): string
    {
        return $this->wooCommerceSettingsPrefix . Gateway::GATEWAY_ID . $this->settingsSuffix;
    }

    private function getApiUrl(): string
    {
        if ($this->getOption('nodalive_is_test') === 'yes') {
            return NodaPayment::API_URL_SENDBOX;
        }

        return NodaPayment::API_URL_LIVE;
    }

    private function getRedirectAfterPaymentUrl(WC_Order $order = null): string
    {
        $redirectKey = $this->getOption( 'nodalive_redirect_after_payment' );

        switch ($redirectKey) {
            case static::$paymentRedirectOptionShop:
                $url = get_permalink(wc_get_page_id( 'shop' ));
                break;
            case static::$paymentRedirectOptionCart:
                $url = wc_get_cart_url();
                break;
            case static::$paymentRedirectOptionHome:
                $url = home_url();
                break;
            case static::$paymentRedirectOptionOrdersList:
                $url = wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );
                break;
            case static::$paymentRedirectOptionCurrentOrder:
                if (!$order) {
                    $url = wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );
                } else {
                    $url = $order->get_view_order_url();
                }
                break;
            default:
                $url = get_permalink(wc_get_page_id( 'shop' ));
        }

        return $url;
    }

    public function getMainConfigs(): array {
        return [
            'test_mode' => $this->getOption('nodalive_is_test') === 'yes',
            'api_key'   => $this->getOption('nodalive_api_key'),
        ];
    }
}
