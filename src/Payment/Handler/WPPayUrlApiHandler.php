<?php

namespace NodaPay\Payment\Handler;

use NodaPay\Payment\Abstracts\GenericResponse;
use NodaPay\Payment\NodaPayment;
use NodaPay\Payment\Responses\ErrorResponse;
use NodaPay\Payment\Responses\PayUrl;

class WPPayUrlApiHandler extends AbstractWPApiHandler
{
    /**
     * @param array $response
     * @return GenericResponse
     */
    protected function prepareResponse(array $response): GenericResponse
    {
        $body = json_decode($response['body'], true);

        $paymentId = $body['id'];
        $paymentUrl = $body['url'];
        $paymentStatus = $body['status'];

        return new PayUrl($paymentId, $paymentUrl, $paymentStatus);
    }

    /**
     * @param array $response
     * @return ErrorResponse|null
     */
    protected function validateResponse($response)
    {
        $body = json_decode($response['body'], true);
        $errMsg = null;

        if (is_array($body)) {
            if (
                !isset($body['url']) ||
                !isset($body['status']) ||
                !isset($body['id'])
            ) {
                $errMsg = 'Missing required fields in payment url response';
            } elseif (isset($body['status']) && !in_array($body['status'], NodaPayment::ALLOWED_STATUSES, true)) {
                $errMsg = "Invalid payment status \"" . $body['status'] . "\". Supported statuses are: " . implode(', ', NodaPayment::ALLOWED_STATUSES);
            }
        } else {
            $errMsg = "Response body is empty";
        }

        return $errMsg ? new ErrorResponse(intval($response['response']['code']), $errMsg) : null;
    }
}
