<?php declare(strict_types=1);

namespace NodaPay\Contracts\Verification;

interface VerifiesVendor {

	/**
	 * Determines if the vendor (e.g., a specific plugin or service) is active.
	 *
	 * @return bool True if the vendor is active, false otherwise.
	 */
	public function is_vendor_active(): bool;

	/**
	 * Checks if the given vendor version is compatible.
	 *
	 * @param string $version The vendor version to check.
	 *
	 * @return bool True if the vendor version is compatible, false otherwise.
	 */
	public function is_vendor_version_compatible( string $version ): bool;

}
