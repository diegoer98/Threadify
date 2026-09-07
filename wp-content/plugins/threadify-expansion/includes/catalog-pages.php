<?php
/**
 * Threadify Expansion — /catalog/ and /industries/ (BETA DRAFTS)
 *
 * Two routes into the same 3,898-style SanMar range:
 *
 *   /catalog/     browse by what the garment IS   (T-shirt, polo, cap, bag…)
 *   /industries/  browse by what the customer DOES (construction, culinary…)
 *
 * Counts, brand tallies, "from" prices and the facet names themselves are all
 * read from the SanMar SDL export rather than written by hand — CATEGORY_NAME
 * is a ';'-joined facet list whose order isn't stable, so it was normalised to
 * single facets before counting. Hero photos are that export's own product
 * images, one non-discontinued style per facet.
 *
 * DRAFT STATUS: both pages are noindex and absent from every nav tree, exactly
 * like /brands/. They are here to be looked at, not shipped.
 *
 * OVERLAP TO RESOLVE: /industries/ covers the same ground as the /shop/ page on
 * the dacuna311/shop-page branch, which already carries this content ported
 * verbatim from the homepage. Only one of the two should survive.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'TSE_CATALOG_SLUG',    'catalog' );
define( 'TSE_INDUSTRIES_SLUG', 'industries' );

/** Garment types. Ordered by how much of the range each covers. */
function tse_catalog_categories() {
	return [
		[ 'name' => 'Sweatshirts & Fleece',  'slug' => 'sweatshirts',  'styles' => 804, 'brands' => 31, 'from' => 1.78 ],
		[ 'name' => 'T-Shirts',              'slug' => 't-shirts',     'styles' => 785, 'brands' => 27, 'from' => 4.08 ],
		[ 'name' => 'Outerwear',             'slug' => 'outerwear',    'styles' => 636, 'brands' => 17, 'from' => 15.98 ],
		[ 'name' => 'Polos & Knits',         'slug' => 'polos',        'styles' => 567, 'brands' => 19, 'from' => 10.56 ],
		[ 'name' => 'Bags',                  'slug' => 'bags',         'styles' => 371, 'brands' => 15, 'from' => 2.04 ],
		[ 'name' => 'Caps & Beanies',        'slug' => 'caps',         'styles' => 365, 'brands' => 16, 'from' => 3.30 ],
		[ 'name' => 'Activewear',            'slug' => 'activewear',   'styles' => 344, 'brands' => 15, 'from' => 5.64 ],
		[ 'name' => 'Woven & Dress Shirts',  'slug' => 'woven-shirts', 'styles' => 213, 'brands' => 13, 'from' => 15.98 ],
		[ 'name' => 'Workwear',              'slug' => 'workwear',     'styles' => 200, 'brands' =>  6, 'from' => 8.92 ],
		[ 'name' => 'Bottoms',               'slug' => 'bottoms',      'styles' => 146, 'brands' => 20, 'from' => 8.98 ],
		[ 'name' => 'Accessories & Aprons',  'slug' => 'accessories',  'styles' => 106, 'brands' => 13, 'from' => 1.78 ],
		[ 'name' => 'Hi-Vis & Protection',   'slug' => 'hi-vis',       'styles' =>  77, 'brands' =>  7, 'from' => 3.18 ],
	];
}

/** Fit and size ranges, which cut across every garment type above. */
function tse_catalog_fits() {
	return [
		[ 'name' => "Women's",           'slug' => 'womens',         'styles' => 969, 'brands' => 32, 'from' => 5.12 ],
		[ 'name' => 'Youth',             'slug' => 'youth',          'styles' => 211, 'brands' => 15, 'from' => 4.08 ],
		[ 'name' => 'Tall',              'slug' => 'tall',           'styles' =>  86, 'brands' => 10, 'from' => 7.40 ],
		[ 'name' => 'Infant & Toddler',  'slug' => 'infant-toddler', 'styles' =>  24, 'brands' =>  4, 'from' => 3.24 ],
		[ 'name' => 'Juniors',           'slug' => 'juniors',        'styles' =>   7, 'brands' =>  1, 'from' => 8.30 ],
	];
}

/** Base URL for the catalog hero photos. */
function tse_catalog_img_base() {
	$u = wp_upload_dir();
	return trailingslashit( $u['baseurl'] ) . 'catalog/';
}

/**
 * One card. $link is where the tile goes; counts are real.
 */
function tse_catalog_card( $item, $link ) {
	$img = tse_catalog_img_base() . $item['slug'] . '.jpg';

	$meta = number_format( $item['styles'] ) . ' styles &middot; ' . $item['brands'] . ' brands';
	$from = $item['from'] ? '<span class="tcat-from">from $' . number_format( $item['from'], 2 ) . '</span>' : '';

	return '
    <a class="tcat-card" href="' . esc_url( $link ) . '">
      <span class="tcat-shot"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async"></span>
      <span class="tcat-body">
        <span class="tcat-name">' . esc_html( $item['name'] ) . '</span>
        <span class="tcat-meta">' . $meta . '</span>
        ' . $from . '
      </span>
    </a>';
}

// ── /catalog/ ───────────────────────────────────────────────────────
function tse_content_catalog() {

	$builder = home_url( '/order-builder/' );

	$html = '
<div class="tse-hero">
  <h1>The Full Catalog</h1>
  <p>3,898 blank styles from 40 brands &mdash; every one of them something we can embroider, print or patch. Browse by what the garment is, or jump to your trade.</p>
  ' . tse_cta_btn( 'Get a Free Quote' ) . '
</div>

<div class="tse-section">
  <h2>Browse by garment</h2>
  <p>Prices shown are the blank garment only, starting at the smallest size. Decoration and the one-time $35 digitizing fee (new embroidery designs only) are quoted separately &mdash; nothing goes on a machine before you approve it.</p>
  <div class="tcat-grid">';

	foreach ( tse_catalog_categories() as $c ) {
		$html .= tse_catalog_card( $c, $builder . '?category=' . $c['slug'] );
	}

	$html .= '
  </div>
</div>

<div class="tse-section tcat-fits">
  <h2>Fits &amp; sizes</h2>
  <p>These cut across every category above &mdash; most styles come in more than one fit.</p>
  <div class="tcat-grid tcat-grid-sm">';

	foreach ( tse_catalog_fits() as $f ) {
		$html .= tse_catalog_card( $f, $builder . '?fit=' . $f['slug'] );
	}

	$html .= '
  </div>
</div>

<div class="tse-section">
  <h2>Two other ways in</h2>
  <div class="tcat-routes">
    <a class="tcat-route" href="' . esc_url( home_url( '/industries/' ) ) . '">
      <strong>Shop by industry</strong>
      <span>Tell us your trade and we&rsquo;ll show the pieces that suit it &mdash; 13 industries, hand-picked.</span>
    </a>
    <a class="tcat-route" href="' . esc_url( home_url( '/brands/' ) ) . '">
      <strong>Shop by brand</strong>
      <span>Carhartt, Nike, The North Face, Port Authority and 35 more.</span>
    </a>
  </div>
</div>';

	return $html . tse_related( [
		'/embroidery/'    => 'Custom Embroidery',
		'/dtf-printing/'  => 'DTF Printing',
		'/custom-patches/'=> 'Custom Patches',
	] );
}

// ── /industries/ ────────────────────────────────────────────────────
function tse_content_industries() {

	$html = '
<div class="tse-hero">
  <h1>Shop by Industry</h1>
  <p>Every trade has pieces that work and pieces that don&rsquo;t. Start here and we&rsquo;ll show you the garments that hold up to your day &mdash; then set quantities, placement and your logo.</p>
  ' . tse_cta_btn( 'Get a Free Quote' ) . '
</div>

<div class="tse-section">
  <h2>Pick your trade</h2>
  <p>Each one opens the order builder pre-filtered to garments that suit that work.</p>
  <div class="tind-grid">';

	foreach ( tse_brand_industries() as $slug => $ind ) {
		$html .= '
    <a class="tind-card" href="' . esc_url( home_url( '/order-builder/?industry=' . $slug ) ) . '">
      <span class="tind-name">' . esc_html( $ind['name'] ) . '</span>
      <span class="tind-blurb">' . esc_html( $ind['blurb'] ) . '</span>
      <span class="tind-go">Browse garments &rarr;</span>
    </a>';
	}

	$html .= '
  </div>
</div>

<div class="tse-section">
  <h2>Not sure which fits?</h2>
  <p>Plenty of jobs straddle two of these. If yours does, browse the whole range by garment type instead &mdash; or just tell us what the crew does and we&rsquo;ll put a list together.</p>
  <div class="tcat-routes">
    <a class="tcat-route" href="' . esc_url( home_url( '/catalog/' ) ) . '">
      <strong>Browse the full catalog</strong>
      <span>3,898 styles by garment type &mdash; tees, polos, fleece, outerwear, caps, bags.</span>
    </a>
    <a class="tcat-route" href="' . esc_url( home_url( '/brands/' ) ) . '">
      <strong>Browse by brand</strong>
      <span>Start from a name you already trust.</span>
    </a>
  </div>
</div>';

	return $html . tse_related( [
		'/embroidery/'    => 'Custom Embroidery',
		'/dtf-printing/'  => 'DTF Printing',
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
			'seo_title'  => 'Blank Apparel Catalog — 3,898 Styles from 40 Brands | Threadify',
			'meta_desc'  => 'Browse the full Threadify catalog: tees, polos, fleece, outerwear, caps, bags and workwear from Carhartt, Nike, Port Authority and more. Embroidered and printed in Federal Way, WA.',
			'content_fn' => 'tse_content_catalog',
			'flag'       => '_tse_catalog_page',
		],
		[
			'slug'       => TSE_INDUSTRIES_SLUG,
			'title'      => 'Shop by Industry',
			'seo_title'  => 'Custom Apparel by Industry — Trades, Medical, Culinary | Threadify',
			'meta_desc'  => 'Find blanks that suit your trade: construction, culinary, medical, hospitality, office, fitness and more. Embroidery, DTF and patches from Threadify in Federal Way, WA.',
			'content_fn' => 'tse_content_industries',
			'flag'       => '_tse_catalog_page',
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
		if ( $page ) update_post_meta( $page->ID, $d['flag'], '1' );
	}
}

/** True on either drafted page. */
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
