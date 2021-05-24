<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://hcareproducts.com/
 * @since      1.0.0
 *
 * @package    Gpc_Product_Min_Max_Quantity
 * @subpackage Gpc_Product_Min_Max_Quantity/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gpc_Product_Min_Max_Quantity
 * @subpackage Gpc_Product_Min_Max_Quantity/includes
 * @author     GPC <info@gsmpartscenter.com>
 */
class Gpc_Product_Min_Max_Quantity_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gpc-product-min-max-quantity',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
