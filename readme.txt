=== Noda Gateway ===
Contributors: nodateam, cybalex
Tags: ecommerce, payments, woo commerce, e-commerce, store, checkout, shop, shopping cart, checkout
Requires at least: 5.3
Tested up to: 6.3.1
Requires PHP: 7.0.3
Stable tag: 1.3.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

== Description ==

Easy and safe way to process online payments using Noda payment plugin

== Prior steps == 

Before proceeding with the plugin installation and configuration, ensure you've completed the onboarding process, signed the contract with Noda and obtained access to the Production API keys. 
If you haven't completed these steps, please visit Noda HUB [https://ui.noda.live/hub/] and follow the provided step-by-step guide.

== Plugin requirements ==

PHP version

Please ensure your PHP version is a minimum of 7.0.3. 2
You can verify this by navigating to 'Tools' > 'Site Health' > 'Info' > 'Server' in your WordPress admin panel.
Any version of PHP between 7.0.3 and 8.2.x is supported.


Wordpress version

Please note that a WordPress version of 5.3 or newer is necessary. 
To check your version, you can follow the same steps as checking the PHP version (refer to 1.1), but this time, it's located in the 'WordPress' tab under 'Tools' > 'Site Health' > 'Info' > 'WordPress'.

== WooCommerce plugin for Wordpress ==

To begin using the WooCommerce Noda Payment plugin, you must first install the WooCommerce plugin in your WordPress platform. 
You can follow this guide [https://woocommerce.com/document/installing-uninstalling-woocommerce/] for detailed instructions on installing the WooCommerce plugin.
You must have a minimum WooCommerce version of 4.5 to ensure compatibility. 
If you have an older version, we recommend updating it. To check your current WooCommerce version, please follow these steps:
> Access the WordPress admin tool
> Navigate to 'Plugins' > 'Installed Plugins'
> In the top right corner of the page, you'll find a search input. Enter 'WooCommerce' and check the version in the results.

Important Notice: Please be aware that the Noda Payment plugin supports only EUR, GBP, CAD, BRL, PLN, BGN and RON currencies.
To verify and configure your WooCommerce shop's currency settings, please follow these steps:
> Access the WordPress admin tool
> Go to 'WooCommerce' > 'General' > 'Currency Settings'

== Plugin installation ==

Installation from archive

Ensure you have the 'noda-gateway.zip' archive file ready for plugin installation. Follow these steps in the admin tool to install the plugin: 
> Access the WordPress admin panel
> Navigate to 'Plugins' > 'Add New' > 'Upload Plugin
> Click 'Choose File' > Select 'noda-gateway.zip'
> Click the 'Install Now' button
After the page reloads, you will see a successful installation message. The plugin is now installed and ready for configuration.

== Configuration of plugin ==

WooCommerce configuration

In order to process payments, you must have your shop configured with at least one item added to the store. 
Please refer to the WooCommerce documentation [https://woocommerce.com/documentation/plugins/woocommerce/getting-started/?utm_medium=referral&utm_source=wordpress.org&utm_campaign=wp_org_repo_listing] for guidance on how to set up your shop.

Plugin Configuration

To view the list of payment methods, navigate to 'WooCommerce' > 'Settings' > 'Payments' in the admin tool. 
The Noda Payment plugin should be listed there (as shown in the screenshot below). Click the 'Manage' button to access the plugin settings.
The default values for 'Api Key', 'Signature', and 'Shop Id', pre-filled upon plugin installation, are intended for testing purposes only. 
In testing mode, payments are processed, but no real money transfers occur.
To transition to live, real payments, follow these steps:
> Disable the 'Enable test mode' option.
> Replace the default  'Api Key', 'Signature', and 'Shop Id' values with your organization's specific credentials, which can be accessed in your Noda HUB personal account [https://ui.noda.live/hub/integration].

== Plugin additional options ==

Redirect after payments

After the payment is completed, users are automatically redirected to a designated page. 
The default selection is the 'Current Order' page, which displays comprehensive order details.

Redirecting anonymous users to the homepage

In situations where the user is not logged into WordPress and is redirected after payment, they will be directed to the login page if the checkbox is selected.
