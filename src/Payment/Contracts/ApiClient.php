<?php declare( strict_types=1 );

namespace NodaPay\Payment\Contracts;


/**
 * Interface representing an API adapter.
 * Provides methods for making GET and POST requests.
 */
interface ApiClient {

	/**
	 * Sends a GET request to the specified URL.
	 *
	 * @param string $url The URL to send the GET request to.
	 * @param array<string, string> $header The request headers.
	 * @param bool $return_transfer Whether to return the transfer as a string of the return value of the function.
	 * @param string $encoding The request encoding.
	 * @param int $max_redirects The maximum number of redirects to follow.
	 * @param int $max_timeout The maximum number of seconds to allow for the entire request.
	 * @param string $http_version The HTTP version to use for the request.
	 *
	 * @return array|null The response from the GET request.
	 */
	public function get(
		string $url,
		array $header,
		bool $return_transfer = true,
		string $encoding = '',
		int $max_redirects = 3,
		int $max_timeout = 10,
		string $http_version = '1.1'
	);

	/**
	 * Sends a POST request to the specified URL.
	 *
	 * @param string $url The URL to send the POST request to.
	 * @param array $body The request body.
	 * @param array<string, string> $header The request headers.
	 * @param bool $return_transfer Whether to return the transfer as a string of the return value of the function.
	 * @param string $encoding The request encoding.
	 * @param int $max_redirects The maximum number of redirects to follow.
	 * @param int $max_timeout The maximum number of seconds to allow for the entire request.
	 * @param string $http_version The HTTP version to use for the request.
	 *
	 * @return array|null The response from the POST request.
	 */
	public function post(
		string $url,
		array $body,
		array $header,
		bool $return_transfer = true,
		string $encoding = '',
		int $max_redirects = 3,
		int $max_timeout = 10,
		string $http_version = '1.1'
	);

}

