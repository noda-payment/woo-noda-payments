<?php declare( strict_types=1 );

namespace NodaPay\Payment;

use NodaPay\Api;
use NodaPay\Instance;
use NodaPay\Payment\Abstracts\GenericResponse;
use NodaPay\Payment\Contracts\ApiHandler;
use NodaPay\Payment\Controllers\BaseNodaController;
use NodaPay\Payment\Controllers\PayCallbackController;
use NodaPay\Payment\Exceptions\CurrencyNotSupportedException;
use NodaPay\Payment\Handler\ApiHandlerFactory;
use NodaPay\Traits\NodaSettings;
use WC_Order;

class NodaPayment {
	use NodaSettings;

	/**
	 * Allowed types for the payment option.
	 *
	 * @var string[]
	 */
	const ALLOWED_TYPES = [ 'bank', 'country', 'noda' ];

	/**
	 * @var string[]
	 */
	const ALLOWED_STATUSES = [ 'New', 'Processing', 'Awaiting', 'Done', 'Failed' ];

	const API_URL_LIVE    = 'https://api.noda.live';
	const API_URL_SENDBOX = 'https://api.stage.noda.live';

	/** @var ApiHandlerFactory */
	private $apiClientFactory;

    public function __construct() {
        $this->setApiHandlerFactoryInstance(new ApiHandlerFactory);
    }

	public function process( WC_Order $wcOrder ): GenericResponse {
		$headers = [
			'Accept'       => BaseNodaController::HEADERS_ACCEPT,
			'Content-Type' => BaseNodaController::HEADERS_CONTENT_TYPE,
			'x-api-key'    => $this->getOption( 'nodalive_api_key' ),
		];

		$currency = $wcOrder->get_currency();

		$customerId = $wcOrder->get_customer_id();

		if ( empty($customerId) ) {
		    $email = $wcOrder->get_billing_email();

		    if ( empty( $email )) {
                $customerId = '0';
            } else {
		        $customerId = hash( 'sha256', $email );
            }
        }

		if ( ! in_array( strtoupper( $currency ), Instance::WOOCOMMERCE_SUPPORTED_CURRENCIES, true ) ) {
			throw new CurrencyNotSupportedException(
				sprintf(
					'Currency "%s" is not supported by payment gateway. Supported currencies are: %s',
					$currency,
					implode( ', ', Instance::WOOCOMMERCE_SUPPORTED_CURRENCIES)
				)
			);
		}

		$body = [
			'amount'      => $wcOrder->get_total(),
			'currency'    => $currency,
			'customerId'  => $customerId,
			'description' => 'Order #' . $wcOrder->get_order_number(),
			'shopId'      => $this->getOption( 'nodalive_shop_id' ),
			'paymentId'   => $wcOrder->get_id(),
			'returnUrl'   => $this->getRedirectAfterPaymentUrl( $wcOrder ),
			'webhookUrl'  => get_site_url() . '/wp-json/' . BaseNodaController::NAMESPACE . '/' . PayCallbackController::ENDPOINT,
		];

        if (!empty($_POST['payment_type'])) {
            $body['providerId'] = $_POST['payment_type'];
        }

		return $this
			->getApiHandler( $this->getApiUrl(), Api::API_PAYMENT_ENDPOINT )
			->handle( Api::API_PAYMENT_ENDPOINT, $body, $headers );
	}

	private function getApiHandler( string $apiUrl, string $endpointUrl ): ApiHandler {
		if ( Api::USE_CORE_API_CLIENT && class_exists( '\NodaLive\Base\Api\Commands\CreatePayment::class' ) ) {
			return $this->apiClientFactory->getCoreApiHandler( $apiUrl, $endpointUrl );
		}

		return $this->apiClientFactory->getWPApiHandler( $apiUrl, $endpointUrl );
	}

    public function setApiHandlerFactoryInstance(ApiHandlerFactory $apiClientFactory): self
    {
        $this->apiClientFactory = $apiClientFactory;

        return $this;
    }
}
