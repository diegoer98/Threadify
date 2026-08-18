<?php
/**
 * Per-product toggle (admin) and the placement / upload fields (storefront).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── Admin: opt a product in ─────────────────────────────────────────
add_action( 'woocommerce_product_options_general_product_data', 'tfo_render_product_toggle' );

function tfo_render_product_toggle() {
	woocommerce_wp_checkbox( [
		'id'          => TFO_ENABLE_META,
		'label'       => 'Decoration options',
		'description' => 'Ask for a placement and a design file before this product can be added to the cart.',
		'desc_tip'    => false,
	] );
}

add_action( 'woocommerce_process_product_meta', 'tfo_save_product_toggle' );

function tfo_save_product_toggle( $post_id ) {
	// WooCommerce has already verified its own nonce by this point.
	$enabled = isset( $_POST[ TFO_ENABLE_META ] ) ? 'yes' : 'no';
	update_post_meta( $post_id, TFO_ENABLE_META, $enabled );
}

// ── Storefront assets ───────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'tfo_enqueue' );

function tfo_enqueue() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) return;
	if ( ! tfo_options_enabled( get_the_ID() ) ) return;

	wp_enqueue_style( 'tfo-options', TFO_URL . 'css/order-options.css', [], TFO_VERSION );
}

/**
 * WooCommerce's add-to-cart form is urlencoded by default, which silently
 * discards file inputs. Flip it to multipart before the fields render.
 */
add_action( 'woocommerce_before_add_to_cart_form', 'tfo_force_multipart_form' );

function tfo_force_multipart_form() {
	if ( ! tfo_options_enabled( get_the_ID() ) ) return;
	?>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		var form = document.querySelector('form.cart');
		if (form) { form.setAttribute('enctype', 'multipart/form-data'); }
	});
	</script>
	<?php
}

// ── Storefront: the fields themselves ───────────────────────────────
add_action( 'woocommerce_before_add_to_cart_button', 'tfo_render_fields' );

function tfo_render_fields() {

	$product_id = get_the_ID();
	if ( ! tfo_options_enabled( $product_id ) ) return;

	$placements = tfo_get_placements();
	$chosen     = isset( $_POST[ TFO_PLACEMENT_KEY ] )
		? sanitize_text_field( wp_unslash( $_POST[ TFO_PLACEMENT_KEY ] ) )
		: '';

	$accept = '.' . implode( ',.', tfo_get_allowed_extensions() );
	?>
	<div class="tfo-options">

		<p class="tfo-field">
			<label for="tfo-placement">
				Placement <abbr class="required" title="required">*</abbr>
			</label>
			<select name="<?php echo esc_attr( TFO_PLACEMENT_KEY ); ?>" id="tfo-placement" required>
				<option value="">Choose a placement…</option>
				<?php foreach ( $placements as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $chosen, $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="tfo-field">
			<label for="tfo-design-file">
				Your design file <abbr class="required" title="required">*</abbr>
			</label>
			<input type="file"
			       name="<?php echo esc_attr( TFO_FILE_KEY ); ?>"
			       id="tfo-design-file"
			       accept="<?php echo esc_attr( $accept ); ?>"
			       required>
			<span class="tfo-hint">
				<?php echo esc_html( strtoupper( implode( ', ', tfo_get_allowed_extensions() ) ) ); ?>
				&middot; up to <?php echo esc_html( size_format( tfo_get_max_upload_bytes() ) ); ?>
			</span>
		</p>

		<?php wp_nonce_field( 'tfo_add_to_cart', 'tfo_nonce' ); ?>
	</div>
	<?php
}

/**
 * On shop/category listings, send opted-in products to their own page instead
 * of offering a one-click add — the AJAX loop button cannot carry a file, so
 * without this an order could reach checkout with no placement and no artwork.
 */
add_filter( 'woocommerce_loop_add_to_cart_link', 'tfo_loop_add_to_cart_link', 10, 3 );

function tfo_loop_add_to_cart_link( $html, $product ) {

	if ( ! $product instanceof WC_Product ) return $html;
	if ( ! tfo_options_enabled( $product->get_id() ) ) return $html;

	return sprintf(
		'<a href="%s" class="button %s">%s</a>',
		esc_url( $product->get_permalink() ),
		esc_attr( 'tfo-select-options' ),
		esc_html__( 'Select options', 'woocommerce' )
	);
}
