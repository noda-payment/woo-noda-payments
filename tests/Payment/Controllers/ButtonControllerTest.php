<?php

namespace NodaPay\Tests\Payment\Controllers;

use NodaPay\Base\Api\Commands\AbstractApiCommand;
use NodaPay\Base\Api\Commands\PreparePayButton;
use NodaPay\Base\DTO\DynamicDataObject;
use NodaPay\Base\DTO\RequestObject;
use NodaPay\Base\DTO\ResponseObject;
use NodaPay\Payment\Controllers\ButtonController;
use PHPUnit\Framework\TestCase;
use WP_REST_Response;

class ButtonControllerTest extends TestCase
{
    public function testGetButton()
    {
        $controller = $this
            ->getMockBuilder(ButtonController::class)
            ->onlyMethods(['getApiInstance', 'getWooCommerceCurrency', 'getUserIPAddress', 'getMainConfigs', 'createRequestDto'])
            ->getMock();

        $controller
            ->expects($this->once())
            ->method('getWooCommerceCurrency')
            ->with()
            ->willReturn('EUR');

        $controller
            ->expects($this->once())
            ->method('getUserIPAddress')
            ->with()
            ->willReturn('192.169.0.1');

        $requestDto = $this->createMock(RequestObject::class);

        $controller
            ->expects($this->once())
            ->method('createRequestDto')
            ->with()
            ->willReturn($requestDto);

        $mainConfigs = [
            'test_mode' => true,
            'api_key'   => 'api_key',
        ];

        $responseDTO = $this->createMock(ResponseObject::class);
        $responseDTO->expects($this->once())->method('getStatus')->willReturn(200);

        $response = $this->createMock(DynamicDataObject::class);
        $response
            ->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive(['type'], ['country'], ['displayName'], ['url'])
            ->willReturnOnConsecutiveCalls('typeValue', 'countryValue', 'displayNameValue', 'urlValue');

        $responseDTO->expects($this->once())->method('getResponse')->with()->willReturn($response);
        
        
        $preparePayButton = $this->createMock(AbstractApiCommand::class);
        $preparePayButton
            ->expects($this->once())
            ->method('process')
            ->with($requestDto)
            ->willReturn($responseDTO);

        $controller
            ->expects($this->once())
            ->method('getApiInstance')
            ->with(PreparePayButton::class, $mainConfigs)
            ->willReturn($preparePayButton);
        
        $controller
            ->expects($this->once())
            ->method('getMainConfigs')
            ->with()
            ->willReturn($mainConfigs);

        $this->assertEquals(new WP_REST_Response(
            [
                'type'         => 'typeValue',
                'country_id'   => 'countryValue',
                'display_name' => 'displayNameValue',
                'url'          => 'urlValue',
            ]
        ), $controller->getButton());
    }
}
