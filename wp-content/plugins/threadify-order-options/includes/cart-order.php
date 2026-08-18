<?php
/**
 * Carries the placement choice and artwork from the product page through the
 * cart and into the order, then surfaces both in the WooCommerce admin.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Validate before the item enters the cart ────────────────────────
add_filter( 'woocommerce_add_to_cart_validation', 'tfo_validate_add_to_cart', 10, 3 );

function tfo_validate_add_to_cart( $passed, $product_id, $quantity ) {

	if ( ! tfo_options_enabled( $product_id ) ) return $passed;

	if ( ! isset( $_POST['tfo_nonce'] ) ||
	     ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tfo_nonce'] ) ), 'tfo_add_to_cart' ) ) {
		wc_add_notice( 'Your session expired. Please reload the page and try again.', 'error' );
		return false;
	}

	$placement = isset( $_POST[ TFO_PLACEMENT_KEY ] )
		? sanitize_text_field( wp_unslash( $_POST[ TFO_PLACEMENT_KEY ] ) )
		: '';

	if ( ! array_key_exists( $placement, tfo_get_placements() ) ) {
		wc_add_notice( 'Please choose where the design should go.', 'error' );
		return false;
	}

	if ( empty( $_FILES[ TFO_FILE_KEY ] ) ) {
		wc_add_notice( 'Please attach your design file.', 'error' );
		return false;
	}

	$valid = tfo_validate_upload( $_FILES[ TFO_FILE_KEY ] );
	if ( is_wp_error( $valid ) ) {
		wc_add_notice( $valid->get_error_message(), 'error' );
		return false;
	}

	return $passed;
}

// ── Attach the choice + stored file to the cart item ────────────────
add_filter( 'woocommerce_add_cart_item_data', 'tfo_add_cart_item_data', 10, 3 );

function tfo_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

	if ( ! tfo_options_enabled( $product_id ) ) return $cart_item_data;

	// Validation above already ran; re-read rather than trusting anything cached.
	$placement = isset( $_POST[ TFO_PLACEMENT_KEY ] )
		? sanitize_text_field( wp_unslash( $_POST[ TFO_PLACEMENT_KEY ] ) )
		: '';

	if ( array_key_exists( $placement, tfo_get_placements() ) ) {
		$cart_item_data[ TFO_PLACEMENT_KEY ] = $placement;
	}

	if ( ! empty( $_FILES[ TFO_FILE_KEY ] ) && ! is_wp_error( tfo_validate_upload( $_FILES[ TFO_FILE_KEY ] ) ) ) {
		$stored = tfo_store_upload( $_FILES[ TFO_FILE_KEY ] );
		if ( ! is_wp_error( $stored ) ) {
			$cart_item_data[ TFO_FILE_KEY ] = $stored;
		}
	}

	// Keeps two otherwise-identical products as separate lines when the
	// placement or the artwork differs.
	$cart_item_data['tfo_unique'] = md5( microtime() . wp_rand() );

	return $cart_item_data;
}

// ── Show it in the cart and at checkout ─────────────────────────────
add_filter( 'woocommerce_get_item_data', 'tfo_display_cart_item_data', 10, 2 );

function tfo_display_cart_item_data( $item_data, $cart_item ) {

	if ( ! empty( $cart_item[ TFO_PLACEMENT_KEY ] ) ) {
		$item_data[] = [
			'key'   => 'Placement',
			'value' => tfo_placement_label( $cart_item[ TFO_PLACEMENT_KEY ] ),
		];
	}

	if ( ! empty( $cart_item[ TFO_FILE_KEY ]['original'] ) ) {
		$item_data[] = [
			'key'   => 'Design file',
			'value' => esc_html( $cart_item[ TFO_FILE_KEY ]['original'] ),
		];
	}

	return $item_data;
}

// ── Persist onto the order line item ────────────────────────────────
add_action( 'woocommerce_checkout_create_order_line_item', 'tfo_create_order_line_item', 10, 4 );

function tfo_create_order_line_item( $item, $cart_item_key, $values, $order ) {

	if ( ! empty( $values[ TFO_PLACEMENT_KEY ] ) ) {
		// Visible meta: shows on the order screen, emails and the thank-you page.
		$item->add_meta_data( 'Placement', tfo_placement_label( $values[ TFO_PLACEMENT_KEY ] ), true );
	}

	if ( ! empty( $values[ TFO_FILE_KEY ]['stored'] ) ) {
		// Underscore-prefixed: hidden from display so the on-disk name never
		// leaks. Rendered deliberately by the handlers below.
		$item->add_meta_data( '_' . TFO_FILE_KEY, $values[ TFO_FILE_KEY ]['stored'], true );
		$item->add_meta_data( '_' . TFO_FILE_KEY . '_name', $values[ TFO_FILE_KEY ]['original'], true );
	}
}

// ── Admin order screen: the download link ───────────────────────────
add_action( 'woocommerce_after_order_itemmeta', 'tfo_admin_order_item_meta', 10, 2 );

function tfo_admin_order_item_meta( $item_id, $item ) {

	if ( ! $item instanceof WC_Order_Item_Product ) return;

	$stored   = $item->get_meta( '_' . TFO_FILE_KEY );
	$original = $item->get_meta( '_' . TFO_FILE_KEY . '_name' );
	if ( ! $stored ) return;

	$order_id = $item->get_order_id();
	$exists   = (bool) tfo_resolve_stored_path( $stored );

	echo '<div class="tfo-admin-design"><strong>Design file:</strong> ';

	if ( $exists ) {
		printf(
			'<a href="%s" class="button button-small">Download %s</a>',
			esc_url( tfo_get_download_url( $order_id, $item_id ) ),
			esc_html( $original ?: 'file' )
		);
	} else {
		printf(
			'<em>%s (no longer on the server)</em>',
			esc_html( $original ?: 'file' )
		);
	}

	echo '</div>';
}

// ── Customer-facing: name only, never a link ────────────────────────
add_action( 'woocommerce_order_item_meta_end', 'tfo_customer_order_item_meta', 10, 3 );

function tfo_customer_order_item_meta( $item_id, $item, $order ) {

	if ( is_admin() ) return;
	if ( ! $item instanceof WC_Order_Item_Product ) return;

	$original = $item->get_meta( '_' . TFO_FILE_KEY . '_name' );
	if ( ! $original ) return;

	printf(
		'<p class="tfo-design-name"><strong>Design file:</strong> %s</p>',
		esc_html( $original )
	);
}

// ── Housekeeping: drop artwork no order refers to ───────────────────
add_action( 'init', 'tfo_schedule_cleanup' );

function tfo_schedule_cleanup() {
	if ( ! wp_next_scheduled( 'tfo_cleanup_orphans' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'tfo_cleanup_orphans' );
	}
}

add_action( 'tfo_cleanup_orphans', 'tfo_cleanup_orphan_files' );

/**
 * Removes abandoned-cart artwork. Only touches files older than 90 days that
 * no order line item references, so paid orders keep their artwork intact.
 */
function tfo_cleanup_orphan_files() {
	global $wpdb;

	$dir = tfo_get_upload_dir();
	if ( ! is_dir( $dir ) ) return;

	$referenced = $wpdb->get_col( $wpdb->prepare(
		"SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = %s",
		'_' . TFO_FILE_KEY
	) );
	$referenced = array_flip( (array) $referenced );

	$cutoff = time() - ( 90 * DAY_IN_SECONDS );

	foreach ( (array) glob( $dir . '*' ) as $path ) {
		$name = basename( $path );

		if ( in_array( $name, [ 'index.php', '.htaccess' ], true ) ) continue;
		if ( isset( $referenced[ $name ] ) ) continue;
		if ( filemtime( $path ) > $cutoff ) continue;

		wp_delete_file( $path );
	}
}

register_deactivation_hook( TFO_DIR . 'threadify-order-options.php', 'tfo_clear_cleanup_schedule' );

function tfo_clear_cleanup_schedule() {
	wp_clear_scheduled_hook( 'tfo_cleanup_orphans' );
}
