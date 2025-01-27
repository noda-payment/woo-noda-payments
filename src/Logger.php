<?php

namespace NodaPay;

class Logger {

	public static $logger;

	const WC_LOG_FILENAME = 'nodapay-gateway';

	public static function log( $message ) {
		if ( ! class_exists( 'WC_Logger' ) ) {
			return;
		}

		if ( empty( self::$logger ) ) {
			self::$logger = wc_get_logger();
		}

		$logMsg = "\n" . 'nodapay-gateway:: ' . $message . "\n";

		self::$logger->debug( $logMsg, [ 'source' => self::WC_LOG_FILENAME ] );
	}
}
