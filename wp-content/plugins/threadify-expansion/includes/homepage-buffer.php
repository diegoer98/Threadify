<?php
/**
 * Threadify Expansion — shared homepage output buffer
 *
 * The homepage is database markup echoed by code-snippets snippet id=6, which
 * exits on template_redirect before wp_footer() ever runs — the live page has
 * no wp-includes assets and no footer output. (tse_inject_homepage_nav() hooks
 * wp_footer and is dead on the homepage for exactly this reason.) Any change
 * to the homepage therefore has to rewrite the HTML on its way out.
 *
 * This opens one buffer, ahead of the snippet, and passes the finished
 * document through the `tse_homepage_html` filter. Each homepage change hooks
 * that filter behind its own on/off flag, so they ship independently of each
 * other. When nothing is hooked, no buffer is opened at all.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Priority 0 so the buffer opens before snippet id=6 echoes the page.
add_action( 'template_redirect', 'tse_homepage_buffer', 0 );

function tse_homepage_buffer() {
	if ( ! is_front_page() && ! is_home() ) return;
	if ( ! has_filter( 'tse_homepage_html' ) ) return;
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
	return apply_filters( 'tse_homepage_html', $html );
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
