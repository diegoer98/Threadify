<?php
/**
 * Threadify Expansion — Menu Creation
 * Called on plugin activation.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function tse_create_menu() {

    $menu_name     = 'Threadify Services';
    $menu_location = 'primary';

    // Check if menu already exists.
    $menu_id = 0;
    $existing = get_term_by( 'name', $menu_name, 'nav_menu' );
    if ( $existing ) {
        $menu_id = $existing->term_id;
        // Remove old items so we can rebuild cleanly.
        $old_items = wp_get_nav_menu_items( $menu_id );
        if ( $old_items ) {
            foreach ( $old_items as $item ) {
                wp_delete_post( $item->ID, true );
            }
        }
    } else {
        $menu_id = wp_create_nav_menu( $menu_name );
    }

    if ( is_wp_error( $menu_id ) ) return;

    /* ── HOME ─────────────────────────────────── */
    $home = tse_add_menu_link( $menu_id, 'Home', home_url('/'), 0 );

    /* ── EMBROIDERY ───────────────────────────── */
    $emb_page = get_page_by_path( 'embroidery' );
    $emb = tse_add_menu_page( $menu_id, 'Custom Embroidery', $emb_page, 0 );

    $emb_children = [
        [ 'slug' => 'embroidery/logo-embroidery',          'label' => 'Logo Embroidery'         ],
        [ 'slug' => 'embroidery/embroidered-polo-shirts',  'label' => 'Polo Shirts'             ],
        [ 'slug' => 'embroidery/embroidered-jackets',      'label' => 'Jackets & Outerwear'     ],
        [ 'slug' => 'embroidery/embroidered-hats-caps',    'label' => 'Hats & Caps'             ],
        [ 'slug' => 'embroidery/embroidered-beanies',      'label' => 'Beanies'                 ],
        [ 'slug' => 'embroidery/embroidered-hoodies',      'label' => 'Hoodies & Sweatshirts'   ],
    ];
    foreach ( $emb_children as $c ) {
        $pg = get_page_by_path( $c['slug'] );
        tse_add_menu_page( $menu_id, $c['label'], $pg, $emb );
    }

    /* ── DTF PRINTING ─────────────────────────── */
    $dtf_page = get_page_by_path( 'dtf-printing' );
    $dtf = tse_add_menu_page( $menu_id, 'DTF Printing', $dtf_page, 0 );

    $dtf_children = [
        [ 'slug' => 'dtf-printing/custom-t-shirts', 'label' => 'Custom T-Shirts'         ],
        [ 'slug' => 'dtf-printing/dtf-for-teams',   'label' => 'Teams & Group Orders'    ],
        [ 'slug' => 'dtf-printing/gang-sheets',      'label' => 'DTF Gang Sheets'        ],
    ];
    foreach ( $dtf_children as $c ) {
        $pg = get_page_by_path( $c['slug'] );
        tse_add_menu_page( $menu_id, $c['label'], $pg, $dtf );
    }

    /* ── OTHER SERVICES ───────────────────────── */
    $digi_page    = get_page_by_path( 'design-digitizing' );
    $patches_page = get_page_by_path( 'custom-patches' );
    tse_add_menu_page( $menu_id, 'Embroidery Digitizing', $digi_page,    0 );
    tse_add_menu_page( $menu_id, 'Custom Patches',        $patches_page, 0 );

    /* ── SERVICE AREAS ────────────────────────── */
    $areas_page = get_page_by_path( 'service-areas' );
    $areas = tse_add_menu_page( $menu_id, 'Service Areas', $areas_page, 0 );

    $cities = [
        'service-areas/seattle'  => 'Seattle',
        'service-areas/bellevue' => 'Bellevue',
        'service-areas/kirkland' => 'Kirkland',
        'service-areas/renton'   => 'Renton',
        'service-areas/tacoma'   => 'Tacoma',
        'service-areas/redmond'  => 'Redmond',
        'service-areas/kent'     => 'Kent',
        'service-areas/bothell'  => 'Bothell',
    ];
    foreach ( $cities as $path => $label ) {
        $pg = get_page_by_path( $path );
        tse_add_menu_page( $menu_id, $label, $pg, $areas );
    }

    /* ── FAQ / ABOUT ──────────────────────────── */
    $faq_page   = get_page_by_path( 'faq' );
    $about_page = get_page_by_path( 'about' );
    tse_add_menu_page( $menu_id, 'FAQ',           $faq_page,   0 );
    tse_add_menu_page( $menu_id, 'About',         $about_page, 0 );
    tse_add_menu_link( $menu_id, 'Get a Quote',   home_url('/#contact'), 0 );

    /* ── ASSIGN TO PRIMARY LOCATION ───────────── */
    $locations = get_theme_mod( 'nav_menu_locations' );
    if ( ! is_array( $locations ) ) $locations = [];
    $locations[ $menu_location ] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );
}

/** Helper: add a page post-type menu item. */
function tse_add_menu_page( $menu_id, $label, $page, $parent_id ) {
    if ( ! $page ) return 0;
    return wp_update_nav_menu_item( $menu_id, 0, [
        'menu-item-title'     => $label,
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $page->ID,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
        'menu-item-parent-id' => $parent_id,
    ] );
}

/** Helper: add a custom URL menu item. */
function tse_add_menu_link( $menu_id, $label, $url, $parent_id ) {
    return wp_update_nav_menu_item( $menu_id, 0, [
        'menu-item-title'     => $label,
        'menu-item-url'       => $url,
        'menu-item-type'      => 'custom',
        'menu-item-status'    => 'publish',
        'menu-item-parent-id' => $parent_id,
    ] );
}
