<?php
/**
 * Plugin Name: WooCommerce POS Only Products
 * Plugin URI: https://github.com/iamgabrielma
 * Description: Dev plugin for exploring filtering Point of Sale products to be used in the apps.
 * Version: 0.1.0
 * Author: Gabriel Maldonado
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 * WC tested up to: 9.0
 * Text Domain: wc-pos-only-products
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if WooCommerce is active
 */
if ( ! function_exists( 'is_plugin_active' ) ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    add_action( 'admin_notices', 'wc_pos_products_wc_missing_notice' );
    return;
}

/**
 * Display admin notice if WooCommerce is not active
 */
function wc_pos_products_wc_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e( 'WooCommerce POS Only Products requires WooCommerce to be installed and activated.', 'wc-pos-only-products' ); ?></p>
    </div>
    <?php
}

/**
 * Main plugin class
 */
class WC_POS_Only_Products {

    /**
     * Plugin version
     */
    const VERSION = '0.1.0';

    /**
     * Meta key for product POS availability
     */
    const META_KEY_POS_ALLOWED = '_wc_pos_allowed';

    /**
     * Option key for global POS setting
     */
    const OPTION_KEY_SELL_ALL = 'wc_pos_sell_all_products';

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Hook into exiting Point of Sale settings screen
        add_filter( 'woocommerce_get_settings_point-of-sale', array( $this, 'add_pos_settings' ), 10, 2 );

        // Product meta hooks
        // (Triggers set_default_product_meta when a new product is created, or existing product is updated)
        add_action( 'woocommerce_new_product', array( $this, 'set_default_product_meta' ), 10, 1 );
        add_action( 'woocommerce_update_product', array( $this, 'set_default_product_meta' ), 10, 1 );
    }

    /**
     * Get the default value for POS allowed based on global setting
     *
     * @return string 'yes' or 'no'
     */
    public function get_pos_allowed_default() {
        return get_option( self::OPTION_KEY_SELL_ALL, 'yes' );
    }

    /**
     * Check if a product is allowed in POS
     * TODO: Investigate how this intersects with handling non-simple products that are already not supported
     *
     * @param int|WC_Product $product Product ID or product object
     * @return bool True if product is allowed in POS
     */
    public function is_product_pos_allowed( $product ) {
        if ( is_numeric( $product ) ) {
            $product = wc_get_product( $product );
        }

        if ( ! $product ) {
            return false;
        }

        $pos_allowed = $product->get_meta( self::META_KEY_POS_ALLOWED, true );

        // If meta doesn't exist, use global default
        if ( '' === $pos_allowed ) {
            $pos_allowed = $this->get_pos_allowed_default();
        }

        return 'yes' === $pos_allowed;
    }

    /**
     * Set default POS meta for products
     *
     * @param int $product_id Product ID
     */
    public function set_default_product_meta( $product_id ) {
        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            return;
        }

        // Only set if meta doesn't exist
        $existing_meta = $product->get_meta( self::META_KEY_POS_ALLOWED, true );

        if ( '' === $existing_meta ) {
            $product->update_meta_data( self::META_KEY_POS_ALLOWED, $this->get_pos_allowed_default() );
            $product->save_meta_data();
        }
    }

    /**
     * Add custom settings to Point of Sale settings page
     *
     * @param array  $settings Array of settings
     * @param string $current_section Current section ID
     * @return array Modified settings array
     */
    public function add_pos_settings( $settings, $current_section ) {
        // Add a custom message field to confirm plugin is active
        $custom_settings = array(
            array(
                'title' => __( 'POS Products', 'wc-pos-only-products' ),
                'type'  => 'title',
                'desc'  => '<div style="padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724;">
                    <strong>Plugin activated successfully!</strong><br>
                    WooCommerce POS Only Products plugin is active and running.<br>
                    <em>This is a development plugin for exploring POS product filtering functionality.</em>
                </div>',
                'id'    => 'wc_pos_products_section',
            ),
            array(
                'title'    => __( 'Sell all products in POS', 'wc-pos-only-products' ),
                'desc'     => __( 'Enable this option to make all products available for Point of Sale by default', 'wc-pos-only-products' ),
                'id'       => self::OPTION_KEY_SELL_ALL,
                'default'  => 'yes',
                'type'     => 'checkbox',
                'desc_tip' => __( 'When enabled, all products are marked as available for POS.', 'wc-pos-only-products' ),
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wc_pos_products_section',
            ),
        );

        // Merge with existing settings
        return array_merge( $custom_settings, $settings );
    }
}

// Initialize the plugin
new WC_POS_Only_Products();
