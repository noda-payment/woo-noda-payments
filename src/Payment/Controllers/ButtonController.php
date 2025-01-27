<?php

namespace NodaPay\Payment\Controllers;

use NodaPay\Base\DTO\DynamicDataObject;
use RuntimeException;
use NodaPay\Base\DTO\DataTransferObjectFactory;
use NodaPay\Base\DTO\PreparePayButtonRequest;
use NodaPay\Base\Api\Commands\PreparePayButton;
use NodaPay\Traits\IpAware;
use NodaPay\Traits\NodaSettings;
use Throwable;
use WP_REST_Response;

class ButtonController extends BaseNodaController {

	use IPAware;
	use NodaSettings;

	/**
	 * @var string
	 */
	protected $route = 'button';

	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/' . $this->route,
			[
				'methods'             => BaseNodaController::METHOD_POST,
				'callback'            => [ $this, 'getButton' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	public function getButton(): WP_REST_Response {
		$currency  = $this->getWooCommerceCurrency();
		$ipAddress = $this->getUserIPAddress();

		try {
			$buttonApiInstance = $this->getApiInstance(
				PreparePayButton::class,
				$this->getMainConfigs()
			);

			$requestDTO = $this->createRequestDto( PreparePayButtonRequest::class, $currency, $ipAddress );

			$responseDTO = $buttonApiInstance->process( $requestDTO );
			$response    = $responseDTO->getResponse();

			if ( $responseDTO->getStatus() !== 200 ) {
				throw new RuntimeException( $response->get( 'message' ) . ' Status code: ' . $responseDTO->getStatus() );
			}
		} catch ( Throwable $e ) {
			return new WP_REST_Response( [ 'error' => $e->getMessage() ] );
		}

		return new WP_REST_Response(
			[
				'type'         => $response->get( 'type' ),
				'country_id'   => $response->get( 'country' ),
				'display_name' => $response->get( 'displayName' ),
				'url'          => $response->get( 'url' ),
			]
		);
	}

	public function getWooCommerceCurrency(): string
    {
        return get_woocommerce_currency();
    }

    public function createRequestDto(string $type, string $currency, string $ipAddress): DynamicDataObject {
	    return DataTransferObjectFactory::create(
            $type,
            [
                'currency'  => $currency,
                'ipAddress' => $ipAddress,
            ]
        );
    }
}
