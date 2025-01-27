<?php

namespace NodaPay\Payment\Controllers;

use NodaPay\Base\Api\CommandFactory;
use NodaPay\Base\Api\Commands\AbstractApiCommand;
use NodaPay\Traits\NodaSettings;
use WP_REST_Controller;

abstract class BaseNodaController extends WP_REST_Controller {

	use NodaSettings;

	const METHOD_GET  = 'GET';
	const METHOD_POST = 'POST';

	const HTTP_BAD_REQUEST           = 400;
	const HTTP_OK                    = 200;
	const HTTP_INTERNAL_SERVER_ERROR = 500;

	const HEADERS_ACCEPT       = 'application/json, text/json, text/plain';
	const HEADERS_CONTENT_TYPE = 'application/*+json';

	const NAMESPACE = 'noda';

	/**
	 * @param string $type
	 * @param array  $configs
	 * @return AbstractApiCommand
	 */
	public function getApiInstance( string $type, array $configs ): AbstractApiCommand {
		return $this->getCommandFactory()::create(
			$type,
			$configs
		);
	}

	/**
	 * @return CommandFactory
	 */
	protected function getCommandFactory(): CommandFactory {
		return new CommandFactory();
	}
}
