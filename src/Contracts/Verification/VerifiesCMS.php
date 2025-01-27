<?php declare(strict_types=1);

namespace NodaPay\Contracts\Verification;

interface VerifiesCMS {

	/**
	 * Checks if the current PHP version is compatible.
	 *
	 * @param string $version Minimal compatible PHP version.
	 *
	 * @return bool True if the PHP version is compatible, false otherwise.
	 */
	public function is_php_version_compatible( string $version): bool;

	/**
	 * Checks if the given CMS version is compatible.
	 *
	 * @param string $version The CMS version to check.
	 *
	 * @return bool True if the CMS version is compatible, false otherwise.
	 */
	public function is_cms_version_compatible( string $version ): bool;

}
