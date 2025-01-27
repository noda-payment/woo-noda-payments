<?php declare(strict_types=1);

namespace NodaPay\Contracts\Verification;

interface VerifiesStoreConfig {

	/**
	 * Determines if the store's currency is compatible with the provided list of currencies.
	 *
	 * @param string[] $currencies An array of compatible currencies.
	 *
	 * @return bool True if the store's currency is in the list of compatible currencies, false otherwise.
	 */
	public function is_store_currency_compatible( array $currencies ): bool;

}
