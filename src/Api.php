<?php

namespace NodaPay;

use NodaPay\Payment\Controllers\ButtonController;
use NodaPay\Payment\Controllers\CreatePayment;
use NodaPay\Payment\Controllers\PayCallbackController;

class Api
{
    const USE_CORE_API_CLIENT = false;

    const API_PAYMENT_ENDPOINT = '/api/payments';
    const API_BUTTON_ENDPOINT = '/api/payments/logo';

    public function __construct()
    {
        add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
    }

    public function registerRoutes()
    {
        $buttonController      = new ButtonController();
        $payCallbackController = new PayCallbackController();
        $payUrlController      = new CreatePayment();

        $buttonController->register_routes();
        $payCallbackController->register_routes();
        $payUrlController->register_routes();
    }
}
