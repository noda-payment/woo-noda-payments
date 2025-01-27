<?php

namespace NodaPay\Tests\Payment\Controllers;

use Generator;
use NodaPay\Payment\Controllers\BaseNodaController;
use NodaPay\Payment\Controllers\PayCallbackController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WC_Order;
use WP_REST_Request;

class PayCallbackControllerTest extends TestCase {

	const ORDER_ID         = 3241;
	const INVALID_CURRENCY = 'CHF';
	const VALID_CURRENCY = 'EUR';
	const EXPECTED_SIGNATURE = '90f6f572e8a6c80943192c9c74aec79522a9d298eae77e3948a8986186a43a89';
	const SIGNATURE_KEY = 'valid_signature';

	/**
	 * @var MockObject|WC_Order
	 */
	private $wcOrder;

	protected function setUp(): void {
		$this->wcOrder = $this->createMock( WC_Order::class );
	}

	/**
	 * @dataProvider invalidRequestProvider
	 * @param array $requestData
	 * @param array $expectedErrors
	 * @param int   $getOptionCalledTimes
	 */
	public function testUpdateOrderWithErrors( array $requestData, array $expectedErrors, int $getOptionCalledTimes ) {

		$controller = $this->getMockBuilder( PayCallbackController::class )->onlyMethods( [ 'getWCOrder', 'getOption' ] )->getMock();
		$controller
			->expects( $this->exactly( $getOptionCalledTimes ) )
			->method( 'getOption' )
			->withConsecutive( [ 'nodalive_signature_key' ] )
			->willReturnOnConsecutiveCalls( hash( 'sha256', 'invalid_signature' ) );

		$request = $this->createMock( WP_REST_Request::class );
		$request
			->expects( $this->once() )
			->method( 'get_json_params' )
			->with()
			->willReturn( $requestData );

		$controller
			->expects( isset( $requestData['MerchantPaymentId'] ) ? $this->once() : $this->never() )
			->method( 'getWCOrder' )
			->with( 'wc_order_id')
			->willReturn( $this->wcOrder );

		$this->wcOrder
			->expects( isset( $requestData['MerchantPaymentId'] ) && isset( $requestData['Currency'] ) ? $this->once() : $this->never() )
			->method( 'get_currency' )
			->willReturn( self::INVALID_CURRENCY );

		$this->wcOrder
			->expects( isset( $requestData['MerchantPaymentId'] ) && isset( $requestData['Amount'] ) ? $this->once() : $this->never() )
			->method( 'get_total' )
			->with()
			->willReturn( 99.99 );

		$response = $controller->updateOrder( $request );

		$this->assertEquals( $response->get_status(), BaseNodaController::HTTP_BAD_REQUEST );
		$this->assertEquals(
			[
				'result' => 'failure',
				'errors' => $expectedErrors,
			],
			$response->get_data()
		);
	}

	public function invalidRequestProvider(): Generator {

		yield [
			[],
			[
				'Mandatory field "Status" is missing in request',
				'Mandatory field "PaymentId" is missing in request',
				'Mandatory field "Amount" is missing in request',
				'Mandatory field "Currency" is missing in request',
				'Mandatory field "MerchantPaymentId" is missing in request',
				'Mandatory field "Signature" is missing in request',
			],
			0,
		];

		yield [
			[ 'PaymentId' => self::ORDER_ID ],
			[
				'Mandatory field "Status" is missing in request',
				'Mandatory field "Amount" is missing in request',
				'Mandatory field "Currency" is missing in request',
				'Mandatory field "MerchantPaymentId" is missing in request',
				'Mandatory field "Signature" is missing in request',
			],
			0,
		];

		yield [
			[
				'PaymentId' => self::ORDER_ID,
				'Signature' => '123',
				'Amount'    => 100,
				'Currency'  => self::INVALID_CURRENCY,
			],
			[
				'Mandatory field "Status" is missing in request',
				'Mandatory field "MerchantPaymentId" is missing in request',
			],
			0,
		];

		yield [
			[
				'PaymentId'         => self::ORDER_ID,
				'Signature'         => '123',
				'Amount'            => 100,
				'Currency'          => self::INVALID_CURRENCY,
				'Status'            => 'Failed',
				'MerchantPaymentId' => 'wc_order_id',
			],
			[
				'Invalid merchant id',
				'Invalid order amount',
				'Invalid signature',
			],
			1,
		];
	}

	public function testUpdateOrderSuccess() {

	    $controller = $this->getMockBuilder( PayCallbackController::class )->onlyMethods( [ 'getWCOrder', 'getOption' ] )->getMock();
        $controller
            ->expects( $this->once() )
            ->method( 'getOption' )
            ->withConsecutive( [ 'nodalive_signature_key' ] )
            ->willReturnOnConsecutiveCalls( self::SIGNATURE_KEY );

        $request = $this->createMock( WP_REST_Request::class );
        $request
            ->expects( $this->once() )
            ->method( 'get_json_params' )
            ->with()
            ->willReturn( [
                'PaymentId'         => 'noda_order_id',
                'Signature'         => '4e19778f2b595e85bae17a7de80368e6834b950232561adc809f38d05ba1785e',
                'Amount'            => 99.99,
                'Currency'          => self::VALID_CURRENCY,
                'Status'            => 'Done',
                'MerchantPaymentId' => 'wc_order_id',
            ] );

        $controller
            ->expects( $this->once())
            ->method( 'getWCOrder' )
            ->with( 'wc_order_id' )
            ->willReturn( $this->wcOrder );

        $this->wcOrder
            ->expects( $this->once())
            ->method( 'get_currency' )
            ->willReturn( self::VALID_CURRENCY );

        $this->wcOrder
            ->expects($this->once())
            ->method('get_meta')
            ->with('noda_payment_id')
            ->willReturn('noda_order_id');

        $this->wcOrder
            ->expects( $this->once())
            ->method( 'get_total' )
            ->with()
            ->willReturn( 99.99 );

        $this->wcOrder->expects($this->once())->method('update_status')->with('completed');
        $this->wcOrder->expects($this->once())->method('get_status')->with()->willReturn('completed');

        $response = $controller->updateOrder( $request );

        $this->assertEquals( $response->get_status(), BaseNodaController::HTTP_OK );
        $this->assertEquals(
            [
                'result' => 'success',
                'order_status' => 'completed',
            ],
            $response->get_data()
        );
    }
}
