<?php

namespace NodaPay\Tests\Payment;

use NodaPay\Payment\Exceptions\CurrencyNotSupportedException;
use NodaPay\Payment\Handler\ApiHandlerFactory;
use NodaPay\Payment\Handler\WPPayUrlApiHandler;
use NodaPay\Payment\NodaPayment;
use NodaPay\Payment\Responses\PayUrl;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WC_Order;

class NodaPaymentTest extends TestCase
{
    const API_KEY_VALUE = 'api_key_value';
    const SHOP_ID_VALUE = 'shop_id_value';

    /**
     * @var MockObject|WC_Order
     */
    private $order;
    
    /** @var MockObject|NodaPayment */
    private $nodaPayment;

    /**
     * @var MockObject|ApiHandlerFactory
     */
    private $apiHandlerFactory;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->order = $this->createMock(WC_Order::class);
        
        $this->nodaPayment = $this->getMockBuilder(NodaPayment::class)
            ->onlyMethods(['getOption'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiHandlerFactory = $this->createMock(ApiHandlerFactory::class);
    }

    public function testProcessThrowsExceptionWithWrongCurrency()
    {
        $this->order->expects($this->once())->method('get_currency')->willReturn('CHF');
        $this->order->expects($this->never())->method('get_total');
        $this->nodaPayment->setApiHandlerFactoryInstance($this->apiHandlerFactory);

        $this->apiHandlerFactory->expects($this->never())->method('getCoreApiHandler');

        $this->nodaPayment
            ->expects($this->once())
            ->method('getOption')
            ->with('nodalive_api_key')
            ->willReturn(self::API_KEY_VALUE);

        $this->expectException(CurrencyNotSupportedException::class);
        $this->expectExceptionMessage(
            'Currency "CHF" is not supported by payment gateway. Supported currencies are: eur, gbp'
        );

        $this->nodaPayment->process($this->order);
    }

    public function testProcess()
    {
        $payUrlResponse = $this->createMock(PayUrl::class);

        $this->order->expects($this->once())->method('get_currency')->willReturn('Eur');
        $this->order->expects($this->once())->method('get_total')->with()->willReturn(15.36);
        $this->order->expects($this->once())->method('get_customer_id')->with()->willReturn('1324');
        $this->order->expects($this->once())->method('get_id')->with()->willReturn('3456');
        $this->order->expects($this->once())->method('get_view_order_url')->with()->willReturn('order_url');
        $this->nodaPayment->setApiHandlerFactoryInstance($this->apiHandlerFactory);
        $apiHandler = $this->createMock(WPPayUrlApiHandler::class);

        $apiHandler
            ->expects($this->once())
            ->method('handle')
            ->with(
                '/api/payments',
                $this->callback(function ( $body ) {
                    $this->assertArrayHasKey('webhookUrl', $body);
                    $this->assertStringContainsString('/wp-json/noda/webhook', $body['webhookUrl']);
                    unset($body['webhookUrl']);

                    $this->assertEquals([
                        'amount' => 15.36,
                        'currency' => 'Eur',
                        'customerId' => '1324',
                        'description' => 'Order #',
                        'shopId' => 'shop_id_value',
                        'paymentId' => '3456',
                        'returnUrl' => 'order_url',
                    ], $body);

                    return true;

                }), [
                    'Accept' => 'application/json, text/json, text/plain',
                    'Content-Type' => 'application/*+json',
                    'x-api-key' => 'api_key_value',
            ])
            ->willReturn($payUrlResponse);
        
        $this->apiHandlerFactory->expects($this->never())->method('getCoreApiHandler');
        $this->apiHandlerFactory->expects($this->once())->method('getWPApiHandler')
            ->with()->willReturn($apiHandler);

        $this->nodaPayment
            ->expects($this->exactly(4))
            ->method('getOption')
            ->withConsecutive(
                ['nodalive_api_key'],
                ['nodalive_shop_id'],
                ['nodalive_redirect_after_payment'],
                ['nodalive_is_test']
            )
            ->willReturnOnConsecutiveCalls(self::API_KEY_VALUE, self::SHOP_ID_VALUE, 'current_order', 'yes');

        $this->nodaPayment->setApiHandlerFactoryInstance($this->apiHandlerFactory);

        $this->assertEquals($payUrlResponse, $this->nodaPayment->process($this->order));
    }
}
