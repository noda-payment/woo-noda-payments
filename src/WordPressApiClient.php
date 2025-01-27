<?php

namespace NodaPay;

use NodaPay\Payment\Contracts\ApiClient;

class WordPressApiClient implements ApiClient
{
    /** @var string  */
    private $apiUrl;

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function get(
		string $url,
		array $header,
		bool $return_transfer = true,
		string $encoding = '',
		int $max_redirects = 3,
		int $max_timeout = 10,
		string $http_version = '1.1'
	) {
		return wp_remote_get( $this->apiUrl . $url, [

		] );
	}

	public function post(
		string $url,
		array $body,
		array $header,
		bool $return_transfer = true,
		string $encoding = '',
		int $max_redirects = 3,
		int $max_timeout = 10,
		string $http_version = '1.1'
	) {
        return wp_remote_post( $this->apiUrl . $url, [
			'headers' => $header,
			'body' => json_encode($body),
			'httpversion' => $http_version,
			'timeout' => $max_timeout,
			'redirection' => $max_redirects,
		] );
	}
}
