<?php declare(strict_types=1);

namespace NodaPay\Contracts;

interface Plugin {

	/**
	 * Handles the plugin activation process.
	 *
	 * This method is typically hooked into the activation hook for the plugin.
	 * It can be used to set default options, create database tables, or perform
	 * other setup tasks.
	 *
	 * @see register_activation_hook()
	 *
	 * @return void
	 */
	public function activate();

	/**
	 * Handles the plugin deactivation process.
	 *
	 * This method is typically hooked into the deactivation hook for the plugin.
	 * It can be used to clear temporary data, unschedule cron events, or perform
	 * other cleanup tasks.
	 *
	 * @see register_deactivation_hook()
	 *
	 * @return void
	 */
	public function deactivate();

	/**
	 * Handles the plugin uninstallation process.
	 *
	 * This method is typically hooked into the uninstall hook for the plugin.
	 * It can be used to delete plugin options, drop custom database tables, or
	 * perform other cleanup tasks to remove all traces of the plugin.
	 *
	 * @see register_uninstall_hook()
	 *
	 * @return void
	 */
	public static function uninstall();

	/**
	 * Initializes the plugin functionality.
	 *
	 * This method is typically hooked into the 'plugins_loaded' action to set up
	 * the plugin's core functionality after all plugins have been loaded.
	 *
	 * @action plugins_loaded
	 *
	 * @return void
	 */
	public function init();

}
