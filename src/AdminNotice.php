<?php declare( strict_types=1 );

namespace NodaPay;

use InvalidArgumentException;

/**
 * AdminNotice Class.
 *
 * Handles the creation and display of admin notices in the WordPress dashboard.
 */
class AdminNotice {

	/**
	 * @var string[]
	 */
	const ALLOWED_TYPES = [ 'error', 'warning', 'success', 'info' ];

	/**
	 * Generates the HTML for an admin notice.
	 *
	 * @param string $message The content of the notice.
	 * @param string $type    The type of notice (e.g., 'error', 'warning', 'success', 'info').
	 */
	public function print_notice( string $message, string $type ) {
		if ( ! in_array( $type, self::ALLOWED_TYPES ) ) {
			throw new InvalidArgumentException( 'Invalid notice type provided' );
		}

		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $type ),
			esc_html( $message )
		);
	}

}

