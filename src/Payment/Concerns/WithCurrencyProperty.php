<?php declare(strict_types=1);

namespace NodaPay\Payment\Concerns;

use InvalidArgumentException;
use NodaPay\Instance;

/**
 * @property-read string $currency
 */
trait WithCurrencyProperty {

	/**
	 * Sets the currency for the payment button.
	 * Ensures that the provided currency is one of the allowed currencies.
	 *
	 * @param string $currency The currency in ISO_4217 format.
	 *
	 * @return void
	 *
	 * @throws InvalidArgumentException If an invalid currency is provided.
	 */
	protected function set_currency( string $currency ) {
		if ( ! in_array( strtoupper($currency), Instance::WOOCOMMERCE_SUPPORTED_CURRENCIES ) ) {
			throw new InvalidArgumentException( 'Invalid currency provided' );
		}

		$this->currency = $currency;
	}
}
