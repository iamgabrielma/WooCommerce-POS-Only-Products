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
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Hook into Point of Sale settings
        add_filter( 'woocommerce_get_settings_point-of-sale', array( $this, 'add_pos_settings' ), 10, 2 );
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
                'id'    => 'wc_pos_products_status',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wc_pos_products_status',
            ),
        );

        // Merge with existing settings
        return array_merge( $custom_settings, $settings );
    }
}

// Initialize the plugin
new WC_POS_Only_Products();
