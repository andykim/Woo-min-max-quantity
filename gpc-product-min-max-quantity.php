<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://hcareproducts.com/
 * @since             1.0.0
 * @package           Gpc_Product_Min_Max_Quantity
 *
 * @wordpress-plugin
 * Plugin Name:       Product Min Max Quantity
 * Plugin URI:        https://hcareproducts.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            GPC
 * Author URI:        https://hcareproducts.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gpc-product-min-max-quantity
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GPC_PRODUCT_MIN_MAX_QUANTITY_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gpc-product-min-max-quantity-activator.php
 */
function activate_gpc_product_min_max_quantity() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gpc-product-min-max-quantity-activator.php';
	Gpc_Product_Min_Max_Quantity_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gpc-product-min-max-quantity-deactivator.php
 */
function deactivate_gpc_product_min_max_quantity() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gpc-product-min-max-quantity-deactivator.php';
	Gpc_Product_Min_Max_Quantity_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gpc_product_min_max_quantity' );
register_deactivation_hook( __FILE__, 'deactivate_gpc_product_min_max_quantity' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gpc-product-min-max-quantity.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gpc_product_min_max_quantity() {

	$plugin = new Gpc_Product_Min_Max_Quantity();
	$plugin->run();

}
run_gpc_product_min_max_quantity();
