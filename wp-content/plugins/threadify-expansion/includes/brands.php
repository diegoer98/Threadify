<?php
/**
 * Threadify Expansion — Brands page (BETA, not for main)
 *
 * Self-contained on purpose. Everything this page needs — data, content body,
 * page creation, styles, meta — lives in this file plus css/brands.css, so the
 * only edit to a shared file is the single require_once in the plugin bootstrap.
 * That keeps this out of the way of the in-flight /shop/ work, which is editing
 * page-content.php, create-pages.php and create-menu.php.
 *
 * BETA STATUS: the page is created on activation but is deliberately
 *   - noindex, nofollow (tse_brands_noindex), and
 *   - absent from every nav tree (nothing added to NAV_ITEMS, $tse_nav or create-menu.php),
 * so it is reachable only by typing /brands/ directly. Remove both when it ships.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'TSE_BRANDS_SLUG', 'brands' );

/**
 * Brand groups shown on the page.
 *
 * Logo files are SanMar's own dealer brand headers, copied into
 * uploads/brands/. Grouping is editorial — SanMar doesn't ship one — and the
 * names were read off the supplied logo art.
 *
 * @return array<string,array{label:string,blurb:string,brands:array<string,string>}>
 */
function tse_brand_groups() {
	return [

		'workwear' => [
			'label' => 'Workwear & Trades',
			'blurb' => 'Built for jobsites, shops and crews that are hard on clothing.',
			'brands' => [
				'Carhartt'           => 'carhartt.jpg',
				'CornerStone'        => 'cornerstone.jpg',
				'Red Kap'            => 'redkap.jpg',
				'Bulwark'            => 'bulwark.jpg',
				'Volunteer Knitwear' => 'volunteer.jpg',
			],
		],

		'outdoor' => [
			'label' => 'Outdoor & Premium',
			'blurb' => 'Technical outerwear and fleece for crews that work or play outside.',
			'brands' => [
				'The North Face'    => 'northface.jpg',
				'Eddie Bauer'       => 'eddiebauer.jpg',
				'Cotopaxi'          => 'cotopaxi.jpg',
				'Outdoor Research'  => 'outdoorresearch.jpg',
				'tentree'           => 'tentree.jpg',
				'Russell Outdoors'  => 'russelloutdoors.jpg',
			],
		],

		'performance' => [
			'label' => 'Performance & Golf',
			'blurb' => 'Moisture-wicking polos, tees and layers for teams, gyms and the course.',
			'brands' => [
				'Sport-Tek'    => 'sporttek.jpg',
				'Nike Golf'    => 'nikegolf.jpg',
				'TravisMathew' => 'travismathew.jpg',
				'Champion'     => 'champion.jpg',
				'A4'           => 'a4.png',
			],
		],

		'basics' => [
			'label' => 'Everyday Tees, Fleece & Basics',
			'blurb' => 'The workhorses — soft tees, hoodies and crews that take embroidery and DTF well.',
			'brands' => [
				'Gildan'            => 'gildan.jpg',
				'BELLA+CANVAS'      => 'bellacanvas.jpg',
				'Comfort Colors'    => 'comfortcolors.jpg',
				'Next Level'        => 'nextlevel.jpg',
				'District'          => 'district.jpg',
				'Port & Company'    => 'portandcompany.jpg',
				'Alternative'       => 'alternative.jpg',
				'Allmade'           => 'allmade.jpg',
				'Jerzees'           => 'jerzees.jpg',
				'Fruit of the Loom' => 'fruitoftheloom.jpg',
				'Anvil'             => 'anvil.jpg',
				'Stanley/Stella'    => 'stanleystella.png',
				'Rabbit Skins'      => 'rabbitskins.jpg',
			],
		],

		'corporate' => [
			'label' => 'Corporate, Headwear & Specialty',
			'blurb' => 'Polished layers, bags, caps and clinic-ready pieces.',
			'brands' => [
				'Port Authority' => 'portauthority.jpg',
				'OGIO'           => 'ogio.jpg',
				'Red House'      => 'redhouse.jpg',
				'Mercer+Mettle'  => 'mercermettle.jpg',
				'Brooks Brothers'=> 'brooksbrothers.jpg',
				'Tommy Bahama'   => 'tommybahama.jpg',
				'New Era'        => 'newera.jpg',
				'Richardson'     => 'richardson.jpg',
				'Spacecraft'     => 'spacecraft.jpg',
				'WonderWink'     => 'wonderwink.jpg',
			],
		],
	];
}

/**
 * Industry entries, mirroring the real keys and copy in
 * uploads/2026/07/catalog-meta.txt so this page and the order builder agree.
 *
 * @return array<string,array{name:string,blurb:string}>
 */
function tse_brand_industries() {
	return [
		'construction' => [ 'name' => 'Construction & Trades',  'blurb' => 'Rugged Carhartt, CornerStone and Red Kap workwear built for the jobsite.' ],
		'automotive'   => [ 'name' => 'Automotive & Trades',    'blurb' => 'Industrial work shirts, snag-proof polos, hi-vis and work pants.' ],
		'medical'      => [ 'name' => 'Medical & Healthcare',   'blurb' => 'Scrubs, lab coats, snag-proof polos and clinic-ready layers.' ],
		'culinary'     => [ 'name' => 'Culinary',               'blurb' => 'Kitchen- and counter-ready aprons, chef wear and soft tees.' ],
		'hospitality'  => [ 'name' => 'Hospitality & Events',   'blurb' => 'Front-of-house aprons, performance polos, tees and bags.' ],
		'office'       => [ 'name' => 'Office & Corporate',     'blurb' => 'Polished polos, dress shirts, soft shells, fleece and bags for the workplace.' ],
		'spirit'       => [ 'name' => 'Spirit Merch',           'blurb' => 'Crowd-pleasing soft tees, hoodies, caps and crews for schools, clubs and teams.' ],
		'education'    => [ 'name' => 'Education & Youth',      'blurb' => 'Youth, toddler and school-ready tees, hoodies and joggers.' ],
		'fitness'      => [ 'name' => 'Fitness & Wellness',     'blurb' => 'Moisture-wicking performance tees, polos, tanks, joggers and hoodies.' ],
		'golf'         => [ 'name' => 'Golf & Country Club',    'blurb' => 'TravisMathew and Nike performance polos built for the course and the clubhouse.' ],
		'outdoor'      => [ 'name' => 'Outdoor & Recreation',   'blurb' => 'The North Face, Eddie Bauer and Cotopaxi outerwear, fleece and bags.' ],
		'headwear'     => [ 'name' => 'Headwear',               'blurb' => 'Structured caps, snapbacks, beanies and visors from New Era, Richardson and Port Authority.' ],
		'bags'         => [ 'name' => 'Bags & Accessories',     'blurb' => 'Totes, backpacks, duffels and coolers — from OGIO, Port Authority and more.' ],
	];
}

/** Base URL for the copied brand logos. */
function tse_brands_logo_base() {
	$uploads = wp_upload_dir();
	return trailingslashit( $uploads['baseurl'] ) . 'brands/';
}

// ── Page body ───────────────────────────────────────────────────────
function tse_content_brands() {

	$logo_base = tse_brands_logo_base();

	$html = '
<div class="tse-hero">
  <h1>Brands We Carry</h1>
  <p>All of your favorite brands in one place.</p>
  ' . tse_cta_btn( 'Get a Free Quote' ) . '
</div>';

	// Brand groups.
	foreach ( tse_brand_groups() as $key => $group ) {

		$html .= '
<div class="tse-section tfb-group" id="tfb-' . esc_attr( $key ) . '">
  <h2>' . esc_html( $group['label'] ) . '</h2>
  <p>' . esc_html( $group['blurb'] ) . '</p>
  <ul class="tfb-grid">';

		foreach ( $group['brands'] as $name => $file ) {
			$html .= '
    <li class="tfb-card">
      <img src="' . esc_url( $logo_base . $file ) . '"
           alt="' . esc_attr( $name ) . '"
           width="200" height="50" loading="lazy" decoding="async">
      <span class="tfb-name">' . esc_html( $name ) . '</span>
    </li>';
		}

		$html .= '
  </ul>
</div>';
	}

	$html .= '
<div class="tse-section">
  <h2>Other ways in</h2>
  <div class="tcat-routes">
    <a class="tcat-route" href="' . esc_url( home_url( '/catalog/' ) ) . '">
      <strong>Browse the full catalog</strong>
      <span>Start from the garment.</span>
    </a>
    <a class="tcat-route" href="' . esc_url( home_url( '/industries/' ) ) . '">
      <strong>Shop by industry</strong>
      <span>Start from your trade.</span>
    </a>
  </div>
</div>

<div class="tse-section tfb-closing">
  <h2>Don&rsquo;t see what you need?</h2>
  <p>Send us the brand and style number and we&rsquo;ll quote it.</p>
  ' . tse_cta_btn( 'Ask About a Brand' ) . '
</div>';

	$html .= tse_related( [
		'/embroidery/'       => 'Custom Embroidery',
		'/dtf-printing/'     => 'DTF Printing',
		'/custom-patches/'   => 'Custom Patches',
		'/design-digitizing/'=> 'Design Digitizing',
	] );

	return $html;
}

// ── Page creation ───────────────────────────────────────────────────
// Registered against the main plugin file so it runs on plugin activation
// alongside tse_activate(), without editing tse_activate() itself.
register_activation_hook( TSE_DIR . 'threadify-expansion.php', 'tse_create_brands_page' );

function tse_create_brands_page() {

	if ( ! function_exists( 'tse_maybe_create_page' ) ) return;

	tse_maybe_create_page( [
		'slug'        => TSE_BRANDS_SLUG,
		'title'       => 'Brands',
		'parent_slug' => '',
		'seo_title'   => 'Brands We Carry — Carhartt, Nike, The North Face & More | Threadify',
		'meta_desc'   => 'Threadify decorates blanks from Carhartt, The North Face, Nike, Port Authority, BELLA+CANVAS and more. Real wholesale brands, embroidered and printed in Federal Way, WA.',
		'content_fn'  => 'tse_content_brands',
	] );

	// Second flag, alongside _tse_service_page, so this file's hooks can target
	// the page by meta instead of a slug lookup on every request.
	$page = get_page_by_path( TSE_BRANDS_SLUG );
	if ( $page ) {
		update_post_meta( $page->ID, '_tse_brands_page', '1' );
	}
}

/** True only on the brands page. */
function tse_is_brands_page() {
	if ( ! is_page() ) return false;
	$post = get_queried_object();
	if ( ! $post || ! isset( $post->ID ) ) return false;
	return get_post_meta( $post->ID, '_tse_brands_page', true ) === '1';
}

// ── Styles ──────────────────────────────────────────────────────────
// Loads after tse-styles so it can lean on that file's :root custom
// properties (--bg, --surface2, --gold, --border, --radius, --max-w)
// instead of restating the palette.
add_action( 'wp_enqueue_scripts', 'tse_brands_enqueue', 20 );

function tse_brands_enqueue() {
	if ( ! tse_is_brands_page() ) return;
	// catalog.css carries the shared .tcat-route cross-links used at the foot
	// of this page, so it loads first.
	wp_enqueue_style( 'tse-catalog', TSE_URL . 'css/catalog.css', [ 'tse-styles' ], TSE_VERSION );
	wp_enqueue_style( 'tse-brands',  TSE_URL . 'css/brands.css',  [ 'tse-catalog' ], TSE_VERSION );
}

// ── Beta guard: keep this page out of search results ────────────────
// Runs at priority 2, ahead of tse_meta_tags() at 3, so the directive is the
// first thing in <head>. Delete this whole block when the page goes live.
add_action( 'wp_head', 'tse_brands_noindex', 2 );

function tse_brands_noindex() {
	if ( ! tse_is_brands_page() ) return;
	echo '<meta name="robots" content="noindex, nofollow">' . "\n";
}
