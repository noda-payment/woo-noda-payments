<?php

namespace NodaPay\Payment\Handler;

use NodaPay\Payment\Abstracts\GenericResponse;
use NodaPay\Payment\Contracts\ApiHandler;
use NodaPay\Payment\Responses\ErrorResponse;
use NodaPay\WordPressApiClient;
use WP_Error;

abstract class AbstractWPApiHandler implements ApiHandler
{
    /**
     * @var WordPressApiClient
     */
    private $apiClient;

    public function __construct(string $apiUrl)
    {
        $this->apiClient = new WordPressApiClient($apiUrl);
    }

    public function handle(
        string $url,
        array $body,
        array $header
    ): GenericResponse {
        $response = $this->apiClient->post($url, $body, $header);
        $errorResponse = $this->handleErrorResponse($response);

        if (!$errorResponse) {
            $errorResponse = $this->validateResponse($response);
        }

        if ($errorResponse) {
            return $errorResponse;
        }

        return $this->prepareResponse($response);
    }

    /**
     * @param array $response
     * @return GenericResponse
     */
    abstract protected function prepareResponse(array $response): GenericResponse;

    /**
     * @param array $response
     * @return ErrorResponse|null
     */
    protected abstract function validateResponse($response);

    /**
     * @var array|WP_Error $response
     * @return ErrorResponse|null
     */
    protected function handleErrorResponse($response)
    {
        if (is_wp_error($response)) {
            /** @var WP_Error $response */
            $errorCodeStr = $response->get_error_code(); // error code is usually a string
            $errorMsg = $response->get_error_message($errorCodeStr);

            return new ErrorResponse(0, $errorMsg);
        }

        if (
            !is_array($response) ||
            !isset($response['response']) ||
            !isset($response['body']) ||
            !isset($response['response']['code'])
        ) {
            return new ErrorResponse(0, 'Something went wrong: api response is empty or malformed');
        }

        $responseArray = $response['response'];

        $responseCode = intval($responseArray['code']);

        switch (true) {
            case ($responseCode === 400 || $responseCode === 500):
                $errorResponse = $this->handleApiError($response, $responseCode);
                break;
            case ($responseCode < 400):
                $errorResponse = null;
                break;
            default:
                $errorResponse = $this->handleGenericError($response, $responseCode);
        }

        if ($errorResponse) {
            return $errorResponse;
        }

        json_decode($response['body'], true);

        $errMsg = null;

        if (
            json_last_error() !== JSON_ERROR_NONE
        ) {
            $errMsg = 'Failed to parse response from payment gateway';
        }

        return $errMsg === null ? null : new ErrorResponse($responseCode, $errMsg);
    }

    protected function handleGenericError(array $response, int $responseCode)
    {
        if ($responseCode < 400) {
            return null;
        }

        $errorMsg = '';

        if (isset($response['response']['message']) && is_string($response['response']['message'])) {
            $errorMsg .= $response['response']['message'];
        }

        if (is_string($response['body'])) {
            $errorMsg .= ': ';
            $errorMsg .= $response['body'];
        }

        return new ErrorResponse($responseCode, $errorMsg);
    }

    protected function handleApiError(array $response, int $responseCode)
    {
        if (empty($response['body'])) {
            return new ErrorResponse($responseCode, 'Something went wrong');
        }

        $body = json_decode($response['body'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new ErrorResponse($responseCode, 'Failed to parse response from pay url endpoint');
        }

        if (
            !isset($body['Errors']) ||
            empty($body['Errors'])
        ) {
            return new ErrorResponse($responseCode, 'Something went wrong');
        }

        $errorMsg = '';

        $errors = $body['Errors'];
        foreach ($errors as $errorItem) {
            if (isset($errorItem['Message'])) {
                $errorMsg .= ' ' . $errorItem['Message'];
            }
        }

        $errorMsg = trim($errorMsg);

        return new ErrorResponse($responseCode, $errorMsg);
    }
}
