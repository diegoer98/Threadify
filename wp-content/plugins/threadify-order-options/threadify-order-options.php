<?php
/**
 * Plugin Name: Threadify Order Options
 * Plugin URI: https://threadifyapparel.com
 * Description: Adds a decoration placement selector and a customer design-file upload to WooCommerce products, and carries both through cart, checkout, order emails and the WooCommerce admin.
 * Version: 1.0.0
 * Author: Threadify
 * License: GPL2
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'TFO_VERSION', '1.0.0' );
define( 'TFO_DIR',     plugin_dir_path( __FILE__ ) );
define( 'TFO_URL',     plugin_dir_url( __FILE__ ) );

/**
 * Meta key on the product that switches these options on.
 * Options are opt-in per product so that blanks, gift cards and
 * digitizing services don't demand an artwork file.
 */
define( 'TFO_ENABLE_META', '_tfo_enable_options' );

/** Cart/order item keys. Leading underscore keeps the raw path out of customer-facing meta. */
define( 'TFO_PLACEMENT_KEY', 'tfo_placement' );
define( 'TFO_FILE_KEY',      'tfo_design_file' );

require_once TFO_DIR . 'includes/uploads.php';
require_once TFO_DIR . 'includes/product-fields.php';
require_once TFO_DIR . 'includes/cart-order.php';

// ── Bail out cleanly if WooCommerce isn't active ────────────────────
add_action( 'admin_notices', 'tfo_dependency_notice' );
function tfo_dependency_notice() {
	if ( class_exists( 'WooCommerce' ) ) return;
	if ( ! current_user_can( 'activate_plugins' ) ) return;
	echo '<div class="notice notice-error"><p><strong>Threadify Order Options</strong> requires WooCommerce to be active.</p></div>';
}

// ── Lifecycle ───────────────────────────────────────────────────────
register_activation_hook( __FILE__, 'tfo_activate' );

function tfo_activate() {
	tfo_prepare_upload_dir();
}

/**
 * The placements offered in the dropdown.
 * Filterable so more can be added without touching this plugin.
 *
 * @return array<string,string> value => label
 */
function tfo_get_placements() {
	return apply_filters( 'tfo_placements', [
		'chest'  => 'Chest',
		'sleeve' => 'Sleeve',
		'back'   => 'Back',
	] );
}

/**
 * Whether a given product has the placement/upload options switched on.
 *
 * @param int $product_id
 * @return bool
 */
function tfo_options_enabled( $product_id ) {
	return get_post_meta( (int) $product_id, TFO_ENABLE_META, true ) === 'yes';
}

/**
 * Human-readable label for a stored placement value.
 *
 * @param string $value
 * @return string
 */
function tfo_placement_label( $value ) {
	$placements = tfo_get_placements();
	return $placements[ $value ] ?? $value;
}
