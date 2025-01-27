<?php

declare(strict_types=1);

namespace NodaPay\Tests;

use Generator;
use NodaPay\Gateway;
use NodaPay\Payment\NodaPayment;
use NodaPay\Payment\Responses\ErrorResponse;
use NodaPay\Payment\Responses\PayUrl;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WC_Order;

class GatewayTest extends TestCase
{
    private $orderId = 3214;

    /**
     * @var MockObject|WC_Order
     */
    private $order;

    /**
     * @var NodaPayment|MockObject
     */
    private $paymentService;

    public function setUp(): void
    {
        $this->order = $this->getMockBuilder(WC_Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->paymentService = $this->createMock(NodaPayment::class);
    }

    public function testInit_form_fields()
    {
        $gateway = new Gateway();

        $this->assertArrayHasKey('nodalive_title', $gateway->form_fields);
        $this->assertArrayHasKey('nodalive_is_test', $gateway->form_fields);
        $this->assertArrayHasKey('nodalive_api_key', $gateway->form_fields);
        $this->assertArrayHasKey('nodalive_signature_key', $gateway->form_fields);
        $this->assertArrayHasKey('nodalive_shop_id', $gateway->form_fields);
        $this->assertArrayHasKey('nodalive_redirect_after_payment', $gateway->form_fields);
        $this->assertArrayHasKey('nodalive_description', $gateway->form_fields);

        $this->assertEquals([
            'shop' => 'Shop',
            'cart' => 'Shopping cart',
            'home' => 'Home',
            'orders' => 'Orders',
            'current_order' => 'Current order',
        ],
            $gateway->form_fields['nodalive_redirect_after_payment']['options']
        );
    }

    /**
     * @var int|float $totalAmount
     * @dataProvider invalidAmountProvider
     */
    public function testProcessOrderInvalidAmount($totalAmount)
    {
        $gateway = $this->getMockBuilder(Gateway::class)->onlyMethods(['getWCOrder'])->getMock();
        $gateway->expects($this->once())->method('getWCOrder')->with()->willReturn($this->order);
        $this->order->expects($this->once())->method('get_total')->willReturn($totalAmount);
        $this->order->expects($this->once())->method('payment_complete')->with()->willReturn(true);
        $this->order->expects($this->once())
            ->method('get_checkout_order_received_url')
            ->with()
            ->willReturn('checkout_url');

        $result = $gateway->process_payment( $this->orderId );

        $this->assertEquals(
            ['result' => 'success', 'redirect' => 'checkout_url'],
            $result
        );
    }

    public function testProcessAmountFailureResponse()
    {
        $errorResponse = new ErrorResponse(0, 'Something went wrong');
        $totalAmount = 2.06;
        $gateway = $this->getMockBuilder(Gateway::class)
            ->onlyMethods(['getWCOrder', 'getPaymentServiceInstance'])
            ->getMock();
        $gateway->expects($this->once())->method('getWCOrder')->with()->willReturn($this->order);
        $gateway->expects($this->once())->method('getPaymentServiceInstance')->with()->willReturn($this->paymentService);
        $this->paymentService->expects($this->once())->method('process')->with($this->order)->willReturn($errorResponse);

        $this->order->expects($this->once())->method('get_total')->willReturn($totalAmount);
        $this->order->expects($this->never())->method('payment_complete');
        $this->order->expects($this->once())->method('update_status')->with('failed');

        $result = $gateway->process_payment( $this->orderId );

        $this->assertEquals(['result' => 'fail', 'redirect' => null], $result);
    }

    public function testProcessPayment()
    {
        $payUrl = 'http://pay.somewhere?order=12345';
        $orderTransactionId = '1224';

        $payUrlResponse = $this->createMock(PayUrl::class);
        $totalAmount = 2.07;
        $gateway = $this->getMockBuilder(Gateway::class)
            ->onlyMethods(['getWCOrder', 'getPaymentServiceInstance'])
            ->getMock();
        $gateway->expects($this->once())->method('getWCOrder')->with()->willReturn($this->order);
        $gateway->expects($this->once())->method('getPaymentServiceInstance')->with()->willReturn($this->paymentService);
        $this->paymentService->expects($this->once())->method('process')->with($this->order)->willReturn($payUrlResponse);

        $payUrlResponse->expects($this->once())->method('getUrl')->with()->willReturn($payUrl);
        $payUrlResponse->expects($this->exactly(2))->method('getId')->willReturn($orderTransactionId);

        $this->order->expects($this->once())->method('get_total')->willReturn($totalAmount);
        $this->order->expects($this->never())->method('payment_complete');

        $this->order
            ->expects($this->exactly(2))
            ->method('add_meta_data')
            ->withConsecutive(['payment_link'], ['noda_payment_id']);
        $this->order->expects($this->once())->method('set_transaction_id')->with($orderTransactionId);

        $result = $gateway->process_payment( $this->orderId );

        $this->assertEquals(['result' => 'success', 'redirect' => $payUrl], $result);
    }

    public function invalidAmountProvider(): Generator {
        yield [0];
        yield [-0.01];
        yield [0.00];
    }
}
