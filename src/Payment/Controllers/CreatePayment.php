<?php

namespace NodaPay\Payment\Controllers;

use RuntimeException;
use NodaPay\Base\Api\Commands\CreatePayment as CreatePaymentInstance;
use NodaPay\Payment\Service\PaymentRequestPreparator;
use NodaPay\Traits\IpAware;
use NodaPay\Traits\NodaSettings;
use Throwable;
use WP_REST_Response;

class CreatePayment extends BaseNodaController {

	use IPAware;
	use NodaSettings;

	/**
	 * @var string
	 */
	protected $route = 'create-url';

	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/' . $this->route,
			[
				'methods'             => BaseNodaController::METHOD_POST,
				'callback'            => [ $this, 'getPaymentUrl' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	public function getPaymentUrl(): WP_REST_Response {
		try {
			$createPaymentInstance = $this->getApiInstance( CreatePaymentInstance::class, $this->getMainConfigs() );

			$requestDTO = PaymentRequestPreparator::getRequest();

			$responseDTO = $createPaymentInstance->process( $requestDTO );
			$response    = $responseDTO->getResponse();

			if ( $responseDTO->getStatus() !== parent::HTTP_OK ) {
				throw new RuntimeException(
					$response->get( 'message' ) . ' Status code: ' . $responseDTO->getStatus(),
					$responseDTO->getStatus()
				);
			}
		} catch ( Throwable $e ) {
			return new WP_REST_Response(
				[ 'error' => $e->getMessage() ],
				$e->getCode() !== 0 ? $e->getCode() : parent::HTTP_INTERNAL_SERVER_ERROR
			);
		}

		return new WP_REST_Response(
			[
				'url' => $response->get( 'url' ),
			]
		);
	}
}
