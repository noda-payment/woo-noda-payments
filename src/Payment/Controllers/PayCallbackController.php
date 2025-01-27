<?php

namespace NodaPay\Payment\Controllers;

use NodaPay\Traits\NodaSettings;
use WC_Order;
use WP_REST_Request;
use WP_REST_Response;

class PayCallbackController extends BaseNodaController {

	use NodaSettings;

	const MANDATORY_FIELDS = [ 'Status', 'PaymentId', 'Amount', 'Currency', 'MerchantPaymentId', 'Signature' ];

	const ORDER_STATUS_MAP = [
		'Processing' => 'processing',
		'Failed'     => 'failed',
		'Done'       => 'completed',
	];

	const ENDPOINT = 'webhook';

	/** @var WC_Order */
	private $order;

	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/' . self::ENDPOINT,
			[
				'methods'             => BaseNodaController::METHOD_POST,
				'callback'            => [ $this, 'updateOrder' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	public function updateOrder( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params();

		$errors = $this->validateRequest( $params );

		if ( ! empty( $errors ) ) {
			return new WP_REST_Response(
				[
					'result' => 'failure',
					'errors' => $errors,
				],
				parent::HTTP_BAD_REQUEST
			);
		}

		if ( isset( self::ORDER_STATUS_MAP[ $params['Status'] ] ) ) {
			$this->order->update_status( self::ORDER_STATUS_MAP[ $params['Status'] ] );
		}

		return new WP_REST_Response(
			[
				'result'       => 'success',
				'order_status' => $this->order->get_status(),
			]
		);
	}

	/**
	 * @param mixed $orderId
	 * @return bool|WC_Order|\WC_Order_Refund
	 */
	public function getWCOrder( $orderId ) {
		return wc_get_order( $orderId );
	}

	private function validateRequest( array $params ): array {
		$errors = [];

		foreach ( self::MANDATORY_FIELDS as $mandatoryField ) {
			if ( ! isset( $params[ $mandatoryField ] ) || empty( $params[ $mandatoryField ] ) ) {
				$errors[] = 'Mandatory field "' . $mandatoryField . '" is missing in request';
			}
		}

        if (isset($params['MerchantPaymentId']) && $params['MerchantPaymentId']) {
            $wcOrder = $this->getWCOrder($params['MerchantPaymentId']);

            if ($wcOrder instanceof WC_Order) {
                $this->order = $wcOrder;
            } else {
                $errors[] = 'Invalid order id ' . $params['MerchantPaymentId'];
            }
        }

		if ( isset($params['PaymentId']) && isset($this->order) && $this->order->get_meta('noda_payment_id') !== $params['PaymentId'] ) {
			$errors[] = 'Invalid merchant id';
		}

		if ( isset($params['Status']) && ! in_array( $params['Status'], [ 'Done', 'Processing', 'Failed' ], true ) ) {
			$errors[] = 'Invalid order status';
		}

		if ( $this->order && isset($params['Currency']) && $this->order->get_currency() !== $params['Currency'] ) {
			$errors[] = 'Invalid order currency';
		}

		if ( $this->order && isset($params['Amount'])) {
			$totalFromOrder   = intval( round( $this->order->get_total() * 100 ) );
			$totalFromRequest = intval( round( $params['Amount'] * 100 ) );

			if ( $totalFromOrder !== $totalFromRequest ) {
				$errors[] = 'Invalid order amount';
			}

			if (
			    isset($params['Status'])
                && isset($params['PaymentId'])
                && $params['Status']
                && $this->signatureIsInvalid( $params['Signature'], $params['PaymentId'], $params['Status'] )
            ) {
				$errors[] = 'Invalid signature';
			}
		}

		return $errors;
	}

	private function signatureIsInvalid( $requestSignature, $orderId, $requestStatus ): bool {
		$expectedSignature = hash( 'sha256', $orderId . $requestStatus . $this->getOption( 'nodalive_signature_key' ) );

		return $requestSignature !== $expectedSignature;
	}
}
