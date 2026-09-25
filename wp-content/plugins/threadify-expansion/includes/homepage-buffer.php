<?php
/**
 * Threadify Expansion — shared output buffer for database-rendered pages
 *
 * The homepage and /events/ are database markup echoed by code-snippets
 * snippet id=6, which exits on template_redirect before wp_footer() ever runs
 * — the live pages have no wp-includes assets and no footer output.
 * (tse_inject_homepage_nav() hooks wp_footer and is dead on the homepage for
 * exactly this reason.) Any change to those pages therefore has to rewrite the
 * HTML on its way out.
 *
 * This opens one buffer, ahead of the snippet, and passes the finished
 * document through a per-page filter: `tse_homepage_html` or
 * `tse_events_html`. Each change hooks its page's filter behind its own on/off
 * flag, so they ship independently. When nothing is hooked for the current
 * page, no buffer is opened at all.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Which filter, if any, applies to this request. */
function tse_buffered_page_filter() {
	if ( is_front_page() || is_home() ) return 'tse_homepage_html';
	if ( is_page( 'events' ) )          return 'tse_events_html';
	return '';
}

// Priority 0 so the buffer opens before snippet id=6 echoes the page.
add_action( 'template_redirect', 'tse_homepage_buffer', 0 );

function tse_homepage_buffer() {
	$filter = tse_buffered_page_filter();
	if ( $filter === '' || ! has_filter( $filter ) ) return;
	ob_start( 'tse_homepage_buffer_flush' );
}

/**
 * PHP flushes open buffers — running this callback — at shutdown, so it still
 * fires even though the snippet exits early.
 *
 * @param string $html
 * @return string
 */
function tse_homepage_buffer_flush( $html ) {
	if ( stripos( $html, '</body>' ) === false ) return $html;
	return apply_filters( tse_buffered_page_filter(), $html );
}

/**
 * Inserts markup immediately before the closing body tag.
 *
 * @param string $html
 * @param string $payload
 * @return string
 */
function tse_homepage_before_body_end( $html, $payload ) {
	$pos = strripos( $html, '</body>' );
	if ( $pos === false || $payload === '' ) return $html;
	return substr( $html, 0, $pos ) . $payload . substr( $html, $pos );
}
