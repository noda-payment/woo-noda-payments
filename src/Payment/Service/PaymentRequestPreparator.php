<?php

declare(strict_types=1);

namespace NodaPay\Payment\Service;

use NodaPay\Base\DTO\CreatePaymentRequest;
use NodaPay\Base\DTO\DataTransferObjectFactory;


class PaymentRequestPreparator {

	public static function getRequest( $cart = null ): CreatePaymentRequest {
		$request = DataTransferObjectFactory::create(
			CreatePaymentRequest::class,
			[
				'shop_id' => get_option( 'nodalive_shop_id' ),
			]
		);
		$request->setReturnUrl( 'https://myshop.com/returnurl' )
		->setWebhookUrl( 'https://myshop.com/hookurl' );

		if ( $cart ) {
			$data = array_pop( $cart );
			$user = wp_get_current_user();

			$request
				->setAmount( $data['line_total'] )
				->setCustomerId( $user->get( 'ID' ) )
				->setDescription( 'Order ' . $data['key'] )
				->setPaymentId( $data['key'] );
		}

		return $request;
	}
}
