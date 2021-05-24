<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://hcareproducts.com/
 * @since      1.0.0
 *
 * @package    Gpc_Product_Min_Max_Quantity
 * @subpackage Gpc_Product_Min_Max_Quantity/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gpc_Product_Min_Max_Quantity
 * @subpackage Gpc_Product_Min_Max_Quantity/admin
 * @author     GPC <info@gsmpartscenter.com>
 */
class Gpc_Product_Min_Max_Quantity_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('woocommerce_product_options_inventory_product_data', array(&$this, 'wc_qty_add_product_field'));
        add_action( 'woocommerce_process_product_meta', array(&$this, 'wc_qty_save_product_field') );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Gpc_Product_Min_Max_Quantity_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Gpc_Product_Min_Max_Quantity_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/gpc-product-min-max-quantity-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Gpc_Product_Min_Max_Quantity_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Gpc_Product_Min_Max_Quantity_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/gpc-product-min-max-quantity-admin.js', array('jquery'), $this->version, false);

    }

    public function wc_qty_add_product_field()
    {

        echo '<div class="options_group">';
        woocommerce_wp_text_input(
            array(
                'id' => '_wc_min_qty_product',
                'label' => __('Minimum Quantity', 'woocommerce-max-quantity'),
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => __('Optional. Set a minimum quantity limit allowed per order. Enter a number, 1 or greater.', 'woocommerce-max-quantity')
            )
        );
        echo '</div>';

        echo '<div class="options_group">';
        woocommerce_wp_text_input(
            array(
                'id' => '_wc_max_qty_product',
                'label' => __('Maximum Quantity', 'woocommerce-max-quantity'),
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => __('Optional. Set a maximum quantity limit allowed per order. Enter a number, 1 or greater.', 'woocommerce-max-quantity')
            )
        );
        echo '</div>';
    }

    public function wc_qty_save_product_field($post_id)
    {
        $val_min = trim(get_post_meta($post_id, '_wc_min_qty_product', true));
        $new_min = sanitize_text_field($_POST['_wc_min_qty_product']);

        $val_max = trim(get_post_meta($post_id, '_wc_max_qty_product', true));
        $new_max = sanitize_text_field($_POST['_wc_max_qty_product']);

        if ($val_min != $new_min) {
            update_post_meta($post_id, '_wc_min_qty_product', $new_min);
        }

        if ($val_max != $new_max) {
            update_post_meta($post_id, '_wc_max_qty_product', $new_max);
        }
    }
}
