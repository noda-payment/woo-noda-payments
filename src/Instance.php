<?php

declare(strict_types=1);

namespace NodaPay;

use NodaPay\Contracts\Plugin;
use NodaPay\Traits\NodaSettings;
use WC_Order;
use WP_User;

/**
 * Represents the main instance of the NodaPay plugin.
 *
 * @package NodaPay
 */
class Instance implements Plugin {

    /**
     * List of supported currencies for the plugin.
     *
     * @var string[]
     */
    const WOOCOMMERCE_SUPPORTED_CURRENCIES = [ 'GBP', 'EUR', 'PLN', 'CAD', 'BRL', 'PLN', 'BGN', 'RON'];

	use NodaSettings;

	/**
	 * Minimum required PHP version for the plugin.
	 *
	 * @var string
	 */
	const PHP_MIN_VERSION = '7.0.3';

	/**
	 * Minimum required WordPress version for the plugin.
	 *
	 * @var string
	 */
	const WORDPRESS_MIN_VERSION = '5.3';

	/**
	 * Minimum required WooCommerce version for the plugin.
	 *
	 * @var string
	 */
	const WOOCOMMERCE_MIN_VERSION = '4.5';

	/**
	 * Fully qualified class name of the NodaPay payment gateway.
	 *
	 * @var class-string
	 */
	const NODA_PAYMENT_GATEWAY = 'NodaPay\Gateway';

	/**
	 * Base name of the plugin.
	 *
	 * @var string
	 */
	private $basename;

	/**
	 * Admin notice handler.
	 *
	 * @var AdminNotice
	 */
	private $admin_notice;

	/**
	 * Verificator instance for various checks.
	 *
	 * @var Verificator
	 */
	private $verificator;

	/**
	 * Instance constructor.
	 *
	 * @param string      $basename     Base name of the plugin.
	 * @param AdminNotice $admin_notice Admin notice handler.
	 * @param Verificator $verificator  Verificator instance for various checks.
	 */
	public function __construct( string $basename, AdminNotice $admin_notice, Verificator $verificator ) {
		$this->basename     = $basename;
		$this->admin_notice = $admin_notice;
		$this->verificator  = $verificator;

		add_action( 'template_redirect', [ $this, 'redirectNotLoggedInUsers' ] );
	}

	function redirectNotLoggedInUsers() {
		if ( $this->getOption( 'nodalive_redirect_anonymous_users' ) !== 'yes' ) {
			return;
		}

		if ( is_view_order_page() ) {

			$requestUri = $_SERVER['REQUEST_URI'];

			$orderId = intval( get_query_var( 'view-order' ) );

			$orderHasUser = false;
			$order        = null;

			if ( $orderId > 0 ) {
				$order = wc_get_order( $orderId );

				if ( $order instanceof WC_Order ) {
					$orderHasUser = $order->get_user() instanceof WP_User;
				}
			}

			if ( ! is_user_logged_in() ) {
				if ( $orderHasUser ) {
					wp_redirect( wp_login_url( $requestUri ) );
					exit;
				}

				// in case user is not logged in and no user instance is linked to the order - redirect to cart
				wp_redirect( wc_get_cart_url() );
				exit;
			}

			if ( $order ) {
				if ( $order instanceof WC_Order && $order->get_user_id() !== get_current_user_id() ) {
					wp_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ) );
					exit;
				}
			}
		}
	}

	/**
	 * Activation hook callback.
	 */
	public function activate() {
		$errors = $this->verify();
		if ( ! $errors ) {
			return;
		}

		wp_die( wp_kses( implode( '<br>', $errors ), [ '<br>' ] ) );
	}

	/**
	 * Deactivation hook callback.
	 */
	public function deactivate() {
		// Code to run during plugin deactivation.
	}

	/**
	 * Uninstall hook callback.
	 */
	public static function uninstall() {
		// Code to run during plugin uninstallation.
	}

	/**
	 * Initialize the plugin hooks.
	 *
	 * @action plugins_loaded
	 */
	public function init() {
		$errors = $this->verify();

		// Enable gateway if there are no errors.
		if ( ! $errors ) {
			add_filter(
				'woocommerce_payment_gateways',
				function( $gateways ) {
					return array_merge( $gateways, [ self::NODA_PAYMENT_GATEWAY ] );
				}
			);

			return;
		}

		foreach ( $errors as $error ) {
			add_action(
				'admin_notices',
				function() use ( $error ) {
					$this->admin_notice->print_notice( $error, 'error' );
				}
			);
		}

		deactivate_plugins( $this->basename );
	}

	/**
	 * Verifies various conditions and returns an array of error messages for any failed verifications.
	 *
	 * @return string[] Array of error messages.
	 */
	protected function verify(): array {
		$errors = [];

		switch ( true ) {
			case ( ! $this->verificator->is_php_version_compatible( self::PHP_MIN_VERSION ) ):
				// Translators: %s: Minimum required PHP version.
				$errors[] = sprintf( __( 'The PHP version is below the minimum required of %s.', 'noda' ), self::PHP_MIN_VERSION );
				// Fall through.
			case ( ! $this->verificator->is_cms_version_compatible( self::WORDPRESS_MIN_VERSION ) ):
				// Translators: %s: Minimum required WordPress version.
				$errors[] = sprintf( __( 'The WordPress version is below the minimum required of %s.', 'noda' ), self::WORDPRESS_MIN_VERSION );
				// Fall through.
			case ( ! $this->verificator->is_vendor_active() ):
				$errors[] = __( 'WooCommerce is not active.', 'noda' );
				break; // Prevents from following verification.
			case ( ! $this->verificator->is_vendor_version_compatible( self::WOOCOMMERCE_MIN_VERSION ) ):
				// Translators: %s: Minimum required WooCommerce version.
				$errors[] = sprintf( __( 'The WooCommerce version is below the minimum required of %s.', 'noda' ), self::WOOCOMMERCE_MIN_VERSION );
				// Fall through.
			case ( ! $this->verificator->is_store_currency_compatible( self::WOOCOMMERCE_SUPPORTED_CURRENCIES ) ):
				// Translators: %s: List of supported currencies.
				$errors[] = sprintf( __( 'The WooCommerce store currency is not among the supported currencies: %s.', 'noda' ), implode( ', ', self::WOOCOMMERCE_SUPPORTED_CURRENCIES ) );
		}

		return $errors;
	}
}

