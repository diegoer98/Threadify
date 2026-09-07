<?php
/**
 * Threadify Expansion — /catalog/ and /industries/ (BETA DRAFTS)
 *
 *   /catalog/     browse by what the garment IS   (t-shirt, polo, cap, bag…)
 *   /industries/  browse by what the customer DOES (construction, culinary…)
 *
 * Both are index pages: every tile leads somewhere rather than explaining
 * itself, so a visitor picks a direction before reading anything.
 *
 * Counts are real, read from the SanMar SDL export and limited to styles that
 * are still current and have a photo — 2,998 of them. No prices are shown
 * anywhere: blank cost, decoration and digitizing are quoted together, and a
 * blank-only figure on a tile reads as the finished price.
 *
 * DRAFT STATUS: noindex and absent from every nav tree, like /brands/.
 *
 * OVERLAP TO RESOLVE: /industries/ covers the same ground as /shop/ on the
 * dacuna311/shop-page branch. Only one of the two should survive.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'TSE_CATALOG_SLUG',    'catalog' );
define( 'TSE_INDUSTRIES_SLUG', 'industries' );

/** Garment types, widest range first. Counts are browsable styles. */
function tse_catalog_categories() {
	return [
		[ 'name' => 'Sweatshirts & Fleece', 'slug' => 'sweatshirts',  'styles' => 650 ],
		[ 'name' => 'T-Shirts',             'slug' => 't-shirts',     'styles' => 550 ],
		[ 'name' => 'Outerwear',            'slug' => 'outerwear',    'styles' => 475 ],
		[ 'name' => 'Polos & Knits',        'slug' => 'polos',        'styles' => 397 ],
		[ 'name' => 'Bags',                 'slug' => 'bags',         'styles' => 350 ],
		[ 'name' => 'Caps & Beanies',       'slug' => 'caps',         'styles' => 305 ],
		[ 'name' => 'Activewear',           'slug' => 'activewear',   'styles' => 227 ],
		[ 'name' => 'Workwear',             'slug' => 'workwear',     'styles' => 171 ],
		[ 'name' => 'Bottoms',              'slug' => 'bottoms',      'styles' => 133 ],
		[ 'name' => 'Woven & Dress Shirts', 'slug' => 'woven-shirts', 'styles' => 117 ],
		[ 'name' => 'Accessories & Aprons', 'slug' => 'accessories',  'styles' =>  90 ],
		[ 'name' => 'Hi-Vis & Protection',  'slug' => 'hi-vis',       'styles' =>  67 ],
	];
}

/** Fits and size ranges, which cut across every garment type. */
function tse_catalog_fits() {
	return [
		[ 'name' => "Women's",          'slug' => 'womens',         'styles' => 649 ],
		[ 'name' => 'Youth',            'slug' => 'youth',          'styles' => 172 ],
		[ 'name' => 'Tall',             'slug' => 'tall',           'styles' =>  72 ],
		[ 'name' => 'Infant & Toddler', 'slug' => 'infant-toddler', 'styles' =>  24 ],
	];
}

function tse_catalog_img_base() {
	$u = wp_upload_dir();
	return trailingslashit( $u['baseurl'] ) . 'catalog/';
}

function tse_industry_img_base() {
	$u = wp_upload_dir();
	return trailingslashit( $u['baseurl'] ) . 'industries/';
}

/** One tile: photo, name, count. Nothing else — the tile is a door, not a pitch. */
function tse_catalog_card( $item ) {
	return '
    <a class="tcat-card" href="' . esc_url( home_url( '/catalog/' . $item['slug'] . '/' ) ) . '">
      <span class="tcat-shot">
        <img src="' . esc_url( tse_catalog_img_base() . $item['slug'] . '.jpg' ) . '"
             alt="" loading="lazy" decoding="async">
      </span>
      <span class="tcat-body">
        <span class="tcat-name">' . esc_html( $item['name'] ) . '</span>
        <span class="tcat-meta">' . number_format( $item['styles'] ) . ' styles</span>
      </span>
    </a>';
}

// ── /catalog/ ───────────────────────────────────────────────────────
function tse_content_catalog() {

	$html = '
<div class="tse-hero">
  <h1>The Full Catalog</h1>
  <p>All of your favorite brands in one place.</p>
  ' . tse_cta_btn( 'Get a Free Quote' ) . '
</div>

<div class="tse-section">
  <h2>Browse by garment</h2>
  <div class="tcat-grid">';

	foreach ( tse_catalog_categories() as $c ) {
		$html .= tse_catalog_card( $c );
	}

	$html .= '
  </div>
</div>

<div class="tse-section tcat-fits">
  <h2>Fits &amp; sizes</h2>
  <div class="tcat-grid tcat-grid-sm">';

	foreach ( tse_catalog_fits() as $f ) {
		$html .= tse_catalog_card( $f );
	}

	$html .= '
  </div>
</div>

<div class="tse-section">
  <h2>Other ways in</h2>
  <div class="tcat-routes">
    <a class="tcat-route" href="' . esc_url( home_url( '/industries/' ) ) . '">
      <strong>Shop by industry</strong>
      <span>Start from your trade.</span>
    </a>
    <a class="tcat-route" href="' . esc_url( home_url( '/brands/' ) ) . '">
      <strong>Shop by brand</strong>
      <span>Start from a name you know.</span>
    </a>
  </div>
</div>';

	return $html . tse_related( [
		'/embroidery/'     => 'Custom Embroidery',
		'/dtf-printing/'   => 'DTF Printing',
		'/custom-patches/' => 'Custom Patches',
	] );
}

// ── /industries/ ────────────────────────────────────────────────────
function tse_content_industries() {

	$html = '
<div class="tse-hero">
  <h1>Shop by Industry</h1>
  <p>Gear for every trade in one place.</p>
  ' . tse_cta_btn( 'Get a Free Quote' ) . '
</div>

<div class="tse-section">
  <h2>Pick your trade</h2>
  <div class="tind-grid">';

	// Photo carries the meaning here — a lab coat reads faster than the word
	// "medical" — so the tile is image and name only.
	foreach ( tse_brand_industries() as $slug => $ind ) {
		$html .= '
    <a class="tind-card" href="' . esc_url( home_url( '/order-builder/?industry=' . $slug ) ) . '">
      <span class="tind-shot">
        <img src="' . esc_url( tse_industry_img_base() . $slug . '.jpg' ) . '"
             alt="" loading="lazy" decoding="async">
      </span>
      <span class="tind-name">' . esc_html( $ind['name'] ) . '</span>
    </a>';
	}

	$html .= '
  </div>
</div>

<div class="tse-section">
  <h2>Other ways in</h2>
  <div class="tcat-routes">
    <a class="tcat-route" href="' . esc_url( home_url( '/catalog/' ) ) . '">
      <strong>Browse the full catalog</strong>
      <span>Start from the garment.</span>
    </a>
    <a class="tcat-route" href="' . esc_url( home_url( '/brands/' ) ) . '">
      <strong>Shop by brand</strong>
      <span>Start from a name you know.</span>
    </a>
  </div>
</div>';

	return $html . tse_related( [
		'/embroidery/'        => 'Custom Embroidery',
		'/dtf-printing/'      => 'DTF Printing',
		'/design-digitizing/' => 'Design Digitizing',
	] );
}

// ── Page creation ───────────────────────────────────────────────────
register_activation_hook( TSE_DIR . 'threadify-expansion.php', 'tse_create_catalog_pages' );

function tse_create_catalog_pages() {

	if ( ! function_exists( 'tse_maybe_create_page' ) ) return;

	$defs = [
		[
			'slug'       => TSE_CATALOG_SLUG,
			'title'      => 'Catalog',
			'seo_title'  => 'Blank Apparel Catalog — Carhartt, Nike, The North Face | Threadify',
			'meta_desc'  => 'Browse the full Threadify catalog: tees, polos, fleece, outerwear, caps, bags and workwear, ready for custom embroidery, DTF printing and patches.',
			'content_fn' => 'tse_content_catalog',
		],
		[
			'slug'       => TSE_INDUSTRIES_SLUG,
			'title'      => 'Shop by Industry',
			'seo_title'  => 'Custom Apparel by Industry — Trades, Medical, Culinary | Threadify',
			'meta_desc'  => 'Find blanks that suit your trade: construction, culinary, medical, hospitality, office, fitness and more, from Threadify in Federal Way, WA.',
			'content_fn' => 'tse_content_industries',
		],
	];

	foreach ( $defs as $d ) {
		tse_maybe_create_page( [
			'slug'        => $d['slug'],
			'title'       => $d['title'],
			'parent_slug' => '',
			'seo_title'   => $d['seo_title'],
			'meta_desc'   => $d['meta_desc'],
			'content_fn'  => $d['content_fn'],
		] );

		$page = get_page_by_path( $d['slug'] );
		if ( $page ) update_post_meta( $page->ID, '_tse_catalog_page', '1' );
	}
}

/** True on /catalog/, /industries/ and every browse child. */
function tse_is_catalog_page() {
	if ( ! is_page() ) return false;
	$post = get_queried_object();
	if ( ! $post || ! isset( $post->ID ) ) return false;
	return get_post_meta( $post->ID, '_tse_catalog_page', true ) === '1';
}

// ── Styles ──────────────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'tse_catalog_enqueue', 20 );

function tse_catalog_enqueue() {
	if ( ! tse_is_catalog_page() ) return;
	wp_enqueue_style( 'tse-catalog', TSE_URL . 'css/catalog.css', [ 'tse-styles' ], TSE_VERSION );
}

// ── Draft guard ─────────────────────────────────────────────────────
add_action( 'wp_head', 'tse_catalog_noindex', 2 );

function tse_catalog_noindex() {
	if ( ! tse_is_catalog_page() ) return;
	echo '<meta name="robots" content="noindex, nofollow">' . "\n";
}
