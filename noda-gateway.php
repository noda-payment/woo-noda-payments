<?php

declare(strict_types=1);

/**
 * Plugin Information
 *
 * Plugin Name:       Noda Payment Gateway
 * Plugin URI:        https://noda.live
 * Description:       Effortlessly accept and manage user payments on your online store using the reliable and secure Noda payment gateway solution.
 * Version:           1.3.1
 * Requires at least: 5.3.0
 * Requires PHP:      7.0.3
 * Author:            Noda.Live
 * Author URI:        https://noda.live
 * Developer:         Noda Dev
 * Developer URI:     https://woo.noda.live
 * Text Domain:       noda
 * Domain Path:       /languages
 *
 * WC requires at least: 4.5
 * WC tested up to:     7.9
 */

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$basename   = plugin_basename( __FILE__ );
$autoloader = trailingslashit( __DIR__ ) . 'vendor/autoload.php';

// Autoload.
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}

// Initialise plugin.
$noda_plugin = new NodaPay\Instance(
	$basename,
	new NodaPay\AdminNotice(),
	new NodaPay\Verificator()
);

// register api routes
new NodaPay\Api();

// Activation hook.
register_activation_hook( __FILE__, [ $noda_plugin, 'activate' ] );

// Deactivation hook.
register_deactivation_hook( __FILE__, [ $noda_plugin, 'deactivate' ] );

// Uninstall hook.
register_uninstall_hook( __FILE__, [ \NodaPay\Instance::class, 'uninstall' ] );

// Load the payment gateway.
add_action( 'plugins_loaded', [ $noda_plugin, 'init' ], 100, 0 );
add_action('before_woocommerce_init', function(){
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
});