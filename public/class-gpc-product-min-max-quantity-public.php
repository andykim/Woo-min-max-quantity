<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://hcareproducts.com/
 * @since      1.0.0
 *
 * @package    Gpc_Product_Min_Max_Quantity
 * @subpackage Gpc_Product_Min_Max_Quantity/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gpc_Product_Min_Max_Quantity
 * @subpackage Gpc_Product_Min_Max_Quantity/public
 * @author     GPC <info@gsmpartscenter.com>
 */
class Gpc_Product_Min_Max_Quantity_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_filter( 'woocommerce_quantity_input_args', array(&$this, 'wc_qty_input_args'), 100, 2 );
        add_filter( 'woocommerce_add_to_cart_validation', array($this, 'wc_qty_add_to_cart_validation'), 1, 5 );

        add_filter( 'woocommerce_update_cart_validation', array(&$this, 'wc_qty_update_cart_validation'), 1, 4 );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gpc-product-min-max-quantity-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gpc-product-min-max-quantity-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * @param $args
     * @param $product
     * @return mixed
     */
    public function wc_qty_input_args( $args, $product ) {

        $product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

        $product_min = $this->wc_get_product_min_limit( $product_id );
        $product_max = $this->wc_get_product_max_limit( $product_id );

        if ( ! empty( $product_min ) ) {
            // min is empty
            if ( false !== $product_min ) {
                $args['min_value'] = $product_min;
            }
        }

        if ( ! empty( $product_max ) ) {
            // max is empty
            if ( false !== $product_max ) {
                $args['max_value'] = $product_max;
            }
        }

        if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
            $stock = $product->get_stock_quantity();

            $args['max_value'] = min( $stock, $args['max_value'] );
        }

        // Set step = min_value
        $args['step'] = $args['min_value'];
        return $args;
    }


    /**
     * @param $product_id
     * @return false|int
     */
    public function wc_get_product_max_limit( $product_id ) {
        $qty = get_post_meta( $product_id, '_wc_max_qty_product', true );
        if ( empty( $qty ) ) {
            $limit = false;
        } else {
            $limit = (int) $qty;
        }
        return $limit;
    }

    /**
     * @param $product_id
     * @return false|int
     */
    public function wc_get_product_min_limit( $product_id ) {
        $qty = get_post_meta( $product_id, '_wc_min_qty_product', true );
        if ( empty( $qty ) ) {
            $limit = false;
        } else {
            $limit = (int) $qty;
        }
        return $limit;
    }

    /**
     * @param $passed
     * @param $product_id
     * @param $quantity
     * @param string $variation_id
     * @param string $variations
     * @return false|mixed
     */
    public function wc_qty_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = '', $variations = '' ) {

        $product_min = $this->wc_get_product_min_limit( $product_id );
        $product_max = $this->wc_get_product_max_limit( $product_id );
        $new_max = null;
        $new_min = null;

        if ( ! empty( $product_min ) ) {
            // min is empty
            if ( false !== $product_min ) {
                $new_min = $product_min;
            } else {
                // neither max is set, so get out
                return $passed;
            }
        }

        if ( ! empty( $product_max ) ) {
            // min is empty
            if ( false !== $product_max ) {
                $new_max = $product_max;
            } else {
                // neither max is set, so get out
                return $passed;
            }
        }

        $already_in_cart 	= $this->wc_qty_get_cart_qty( $product_id );
        $product 			= wc_get_product( $product_id );
        $product_title 		= $product->get_title();

        if(!empty( $already_in_cart) && !empty($product_min) && ($already_in_cart + $quantity) % $product_min != 0){
            $passed = false;
            wc_add_notice( apply_filters( 'isa_wc_max_qty_error_message_already_had',
                sprintf( __( 'You can add at least of %1$s %2$s\'s to %3$s. You already have %4$s.', 'gpc-product-min-max-quantity' ),
                    $new_min,
                    $product_title,
                    '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'gpc-product-min-max-quantity' ) . '</a>',
                    $already_in_cart ),
                $new_max,
                $already_in_cart ),
                'error' );
        }
        elseif(empty( $already_in_cart) && !empty($product_min) && $quantity % $product_min != 0){
            $passed = false;
            wc_add_notice( apply_filters( 'isa_wc_max_qty_error_message_already_had',
                sprintf( __( 'You can add at least of %1$s %2$s\'s to %3$s.', 'gpc-product-min-max-quantity' ),
                    $new_min,
                    $product_title,
                    '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'gpc-product-min-max-quantity' ) . '</a>'
                ),
                $new_max,
                $already_in_cart ),
                'error' );
        }
        elseif ( !is_null( $new_max ) && !empty( $already_in_cart ) ) {

            if ( ( $already_in_cart + $quantity ) > $new_max ) {
                // oops. too much.
                $passed = false;

                wc_add_notice( apply_filters( 'isa_wc_max_qty_error_message_already_had',
                    sprintf( __( 'You can add a maximum of %1$s %2$s\'s to %3$s. You already have %4$s.', 'gpc-product-min-max-quantity' ),
                    $new_max,
                    $product_title,
                    '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'gpc-product-min-max-quantity' ) . '</a>',
                    $already_in_cart ),
                    $new_max,
                    $already_in_cart ),
                    'error' );

            }
        }

        return $passed;
    }

    /**
     * @param $product_id
     * @return int
     */
    function wc_qty_get_cart_qty( $product_id ): int
    {
        global $woocommerce;
        $running_qty = 0; // init quantity to 0

        // search the cart for the product in and calculate quantity.
        foreach($woocommerce->cart->get_cart() as $other_cart_item_keys => $values ) {
            if ( $product_id == $values['product_id'] ) {
                $running_qty += (int) $values['quantity'];
            }
        }

        return $running_qty;
    }

    /**
     * @param $product_id
     * @param string $cart_item_key
     * @return int
     */
    public function wc_item_qty_get_cart_qty( $product_id , $cart_item_key = '' ): int
    {
        global $woocommerce;
        $running_qty = 0; // init quantity to 0

        // search the cart for the product in and calculate quantity.
        foreach($woocommerce->cart->get_cart() as $other_cart_item_keys => $values ) {
            if ( $product_id == $values['product_id'] ) {

                if ( $cart_item_key == $other_cart_item_keys ) {
                    continue;
                }

                $running_qty += (int) $values['quantity'];
            }
        }

        return $running_qty;
    }

    /**
     * Validate product quantity when cart is UPDATED
     *
     * @param $passed
     * @param $cart_item_key
     * @param $values
     * @param $quantity
     * @return false|mixed
     */
    public function wc_qty_update_cart_validation( $passed, $cart_item_key, $values, $quantity ) {
        $product_min = $this->wc_get_product_min_limit( $values['product_id'] );
        $product_max = $this->wc_get_product_max_limit( $values['product_id'] );

        $new_min = null;
        $new_max = null;

        if ( ! empty( $product_min ) ) {
            // min is empty
            if ( false !== $product_min ) {
                $new_min = $product_min;
            } else {
                // neither max is set, so get out
                return $passed;
            }
        }

        if ( ! empty( $product_max ) ) {
            // min is empty
            if ( false !== $product_max ) {
                $new_max = $product_max;
            } else {
                // neither max is set, so get out
                return $passed;
            }
        }

        $product = wc_get_product( $values['product_id'] );
        $already_in_cart = $this->wc_item_qty_get_cart_qty( $values['product_id'], $cart_item_key );

        if ( isset( $new_max) && ( $already_in_cart + $quantity ) > $new_max ) {
            wc_add_notice( apply_filters( 'wc_qty_error_message', sprintf( __( 'You can add a maximum of %1$s %2$s\'s to %3$s.', 'gpc-product-min-max-quantity' ),
                $new_max,
                $product->get_name(),
                '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'gpc-product-min-max-quantity' ) . '</a>'),
                $new_max ),
                'error' );
            $passed = false;
        }elseif ( isset( $new_min) && ( $already_in_cart + $quantity )  < $new_min ) {
            wc_add_notice( apply_filters( 'wc_qty_error_message', sprintf( __( 'You should have minimum of %1$s %2$s\'s to %3$s.', 'gpc-product-min-max-quantity' ),
                $new_min,
                $product->get_name(),
                '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'gpc-product-min-max-quantity' ) . '</a>'),
                $new_min ),
                'error' );
            $passed = false;
        }elseif ( isset( $new_min) && !empty($new_min) && ( $already_in_cart + $quantity )  % $new_min != 0 ) {
            wc_add_notice( apply_filters( 'wc_qty_error_message', sprintf( __( 'You should have minimum of %1$s %2$s\'s to %3$s.', 'gpc-product-min-max-quantity' ),
                $new_min,
                $product->get_name(),
                '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'gpc-product-min-max-quantity' ) . '</a>'),
                $new_min ),
                'error' );
            $passed = false;
        }

        return $passed;
    }
}
