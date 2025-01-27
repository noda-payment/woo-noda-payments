<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap file.
 *
 * @package Noda_Live
 */

if (!file_exists(dirname(__DIR__).'/../../../wp-load.php')) {
    throw new \RuntimeException('Tests can be run only if installed in wordpress project');
}

require_once dirname(__DIR__).'/../../../wp-load.php';

if (!class_exists(WooCommerce::class)) {
    throw new \RuntimeException('Woocommerce plugin is not installed');
}

require dirname(__DIR__).'/vendor/autoload.php';

if (!class_exists(\PHPUnit\Framework\TestCase::class)) {
    throw new \RuntimeException('Composer dependencies are not installed for nodapay-woocommerce-gateway plugin');
}
