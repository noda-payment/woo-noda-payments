<?php

namespace NodaPay\Payment\Handler;

use NodaPay\Payment\Abstracts\GenericResponse;
use NodaPay\Payment\Responses\Button;
use NodaPay\Payment\Responses\ErrorResponse;

class WPButtonApiHandler extends AbstractWPApiHandler
{
    protected function prepareResponse(array $response): GenericResponse
    {
        $body = json_decode($response['body'], true);

        $url = $body['url'] ?? null;
        $type = $body['type'] ?? null;
        $id = isset($body['id']) ?? null ;
        $displayName = $body['displayName'] ?? null;
        $country = $body['country'] ?? null;

        return new Button($url, $type, $id, $displayName, $country);
    }

    /**
     * @param array $response
     * @return ErrorResponse|null
     */
    protected function validateResponse($response)
    {
        $body = json_decode($response['body'], true);
        $errorMsg = null;

        if (is_array($body)) {
            if (
                !isset($body['url']) ||
                !isset($body['type'])
            ) {
                $errorMsg = 'Missing required fields in payment url response';
            }
        } else {
            $errorMsg = "Response body is empty";
        }

        return $errorMsg ? new ErrorResponse(0, $errorMsg) : null;
    }
}
