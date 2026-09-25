<?php
/**
 * Threadify Expansion — one set of top tabs on every page (BETA, off)
 *
 * The site renders two different menus. The database pages (home, /events/,
 * /fundraisers/) share one; every plugin page (/about/, /faq/, the service
 * pages, and the new /catalog/, /industries/ and /brands/) builds another in
 * templates/service-page.php. They had drifted apart:
 *
 *   - the database menu still said "Shop" and linked to the homepage's
 *     #industries section, which the catalog move removes;
 *   - the plugin menu had no Catalog, Work or Contact, and listed five service
 *     links separately, which already overflowed a 1280px screen by ~200px.
 *
 * Both now carry the same eight tabs, in the same order:
 *
 *   Catalog · Services · Work · About · FAQ · Contact · Fundraisers · Events
 *
 * On plugin pages Services is a dropdown of the service pages and Service
 * Areas. The plugin menu is reshaped through the `tse_nav_items` filter rather
 * than by editing its list, so the in-flight nav consolidation on another
 * branch merges cleanly; if that tree's "Shop" tab is present it becomes this
 * Catalog tab rather than sitting beside it.
 *
 * Every other link into the removed #industries section is repointed too: the
 * homepage footer's "Shop by Industry" becomes Catalog, the order builder's
 * three ("All industries", "Pick one here", "Start another order") go to
 * /industries/, and the product page's two "browse garments" links and its
 * back-button fallback go to the catalog.
 *
 * Tied to TFB_HOMEPAGE_PATCH, the switch that moves the catalog off the
 * homepage: the Catalog tab and the removed #industries section have to change
 * together, or visitors follow a tab to a section that no longer exists.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( TFB_HOMEPAGE_PATCH ) {
	add_filter( 'tse_db_page_html', 'tse_site_nav_db_filter' );
	add_filter( 'tse_nav_items', 'tse_site_nav_plugin_items' );
	add_action( 'wp_head', 'tse_site_nav_plugin_css', 20 );
}

/**
 * The eight tabs need about 800px. styles.css only collapses the menu to a
 * hamburger below 700px, so between 700 and ~940px the bar ran off the edge.
 * Tabs tighten a little below 1100px so the full bar holds down to 900px, and
 * below that the hamburger takes over — the same 900px line the template's
 * script already uses to switch dropdowns from hover to tap.
 */
function tse_site_nav_plugin_css() {
	if ( ! function_exists( 'tse_is_service_page' ) || ! tse_is_service_page() ) return;
	?>
<style id="tse-site-nav">
@media (max-width: 1100px) {
  .tse-nav__list > li > a { padding: 8px 10px; }
}
@media (max-width: 899px) {
  .tse-nav__list { display: none; flex-direction: column; gap: 0; }
  .tse-nav__list.tse-open {
    display: flex; position: absolute; top: 64px; left: 0; right: 0; z-index: 999;
    padding: 12px 0; background: #F5F0E8; border-bottom: 1px solid var(--border);
  }
  .tse-nav__list > li { width: 100%; }
  .tse-nav__list > li > a { border-radius: 0; padding: 12px 20px; }
  .tse-nav__list > li > ul {
    position: static; width: 100%; min-width: 0; padding: 4px 0;
    border: none; border-top: 1px solid var(--border); box-shadow: none; background: #EDE8DC;
  }
  .tse-nav__list > li > ul > li > a { padding-left: 36px; }
  .tse-nav__toggle { display: block; }
}
</style>
	<?php
}

/**
 * Database pages: "Shop" becomes "Catalog", and the order builder's links
 * into the removed section go to the industries page instead.
 *
 * @param string $html
 * @return string
 */
function tse_site_nav_db_filter( $html ) {

	$html = preg_replace(
		'#<a([^>]*)href="/?\#industries"([^>]*)>\s*Shop\s*</a>#',
		'<a$1href="' . esc_attr( TFB_CATALOG_URL ) . '"$2>Catalog</a>',
		$html, 1
	);

	// Homepage footer — done here rather than left to the carousel script, so
	// it is right even if that script fails to run.
	$html = str_replace( '<a href="#industries">Shop by Industry</a>',
		'<a href="' . esc_attr( TFB_CATALOG_URL ) . '">Catalog</a>', $html );

	// The order builder's links are about choosing an industry.
	if ( is_page( 'order-builder' ) ) {
		$html = str_replace( 'href="/#industries"', 'href="/industries/"', $html );
	}

	// The product page's are about browsing garments — two links, plus the
	// back button's fallback when there is no history to go back to.
	if ( is_page( 'product-details' ) ) {
		$html = str_replace( "'/#industries'", "'" . TFB_CATALOG_URL . "'", $html );
		$html = str_replace( 'href="/#industries"', 'href="' . esc_attr( TFB_CATALOG_URL ) . '"', $html );
	}

	return $html;
}

/**
 * Plugin pages: rebuilds the menu into the site's eight tabs, taking every
 * URL from the incoming list so nothing is hard-coded twice.
 *
 * @param array $nav Items of { label, url, children }.
 * @return array
 */
function tse_site_nav_plugin_items( $nav ) {

	$by = [];
	foreach ( $nav as $item ) $by[ $item['label'] ] = $item;

	$tab = function ( $label, $url, $children = [] ) {
		return [ 'label' => $label, 'url' => $url, 'children' => $children ];
	};

	// Grouped under one tab; their own sub-pages stay reachable from each page.
	$service_labels = [ 'Custom Embroidery', 'DTF Printing', 'Custom Patches', 'Embroidery Digitizing', 'Service Areas' ];
	$services = [];
	foreach ( $service_labels as $l ) {
		if ( isset( $by[ $l ] ) ) $services[] = [ 'label' => $l, 'url' => $by[ $l ]['url'] ];
	}

	$out = [
		$tab( 'Catalog', home_url( TFB_CATALOG_URL ) ),
		$tab( 'Services', home_url( '/#services' ), $services ),
		$tab( 'Work', home_url( '/#work' ) ),
	];
	foreach ( [ 'About', 'FAQ' ] as $l ) {
		if ( isset( $by[ $l ] ) ) $out[] = $tab( $l, $by[ $l ]['url'] );
	}
	$out[] = $tab( 'Contact', home_url( '/#contact' ) );
	foreach ( [ 'Fundraisers', 'Events' ] as $l ) {
		if ( isset( $by[ $l ] ) ) $out[] = $tab( $l, $by[ $l ]['url'] );
	}

	// Anything this list doesn't know about is kept rather than dropped.
	$known = array_merge( $service_labels, [ 'Shop', 'Catalog', 'Services', 'Work', 'About', 'FAQ', 'Contact', 'Fundraisers', 'Events' ] );
	foreach ( $nav as $item ) {
		if ( ! in_array( $item['label'], $known, true ) ) $out[] = $item;
	}

	return $out;
}
