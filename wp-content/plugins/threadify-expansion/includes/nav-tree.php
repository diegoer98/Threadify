<?php
/**
 * Threadify Expansion — Canonical Navigation Tree
 *
 * Single source of truth for site navigation. Previously this list was
 * hand-duplicated in three places (service-page.php's $tse_nav, the
 * homepage-injection script's NAV_ITEMS, and the orphaned create-menu.php
 * nav_menu builder) and drifted independently. Everything that needs the
 * nav tree — server-rendered PHP or the homepage's injected JS — should
 * read from tse_nav_tree() instead of keeping its own copy.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function tse_nav_tree() {
    return [
        [
            'label'    => 'Shop',
            'url'      => home_url( '/shop/' ),
            'children' => [],
        ],
        [
            'label'    => 'Custom Embroidery',
            'url'      => home_url( '/embroidery/' ),
            'children' => [
                [ 'label' => 'Logo Embroidery',        'url' => home_url( '/embroidery/logo-embroidery/' ) ],
                [ 'label' => 'Polo Shirts',             'url' => home_url( '/embroidery/embroidered-polo-shirts/' ) ],
                [ 'label' => 'Jackets & Outerwear',     'url' => home_url( '/embroidery/embroidered-jackets/' ) ],
                [ 'label' => 'Hats & Caps',             'url' => home_url( '/embroidery/embroidered-hats-caps/' ) ],
                [ 'label' => 'Beanies',                 'url' => home_url( '/embroidery/embroidered-beanies/' ) ],
                [ 'label' => 'Hoodies & Sweatshirts',   'url' => home_url( '/embroidery/embroidered-hoodies/' ) ],
            ],
        ],
        [
            'label'    => 'DTF Printing',
            'url'      => home_url( '/dtf-printing/' ),
            'children' => [
                [ 'label' => 'Custom T-Shirts',      'url' => home_url( '/dtf-printing/custom-t-shirts/' ) ],
                [ 'label' => 'Teams & Group Orders',  'url' => home_url( '/dtf-printing/dtf-for-teams/' ) ],
                [ 'label' => 'DTF Gang Sheets',       'url' => home_url( '/dtf-printing/gang-sheets/' ) ],
            ],
        ],
        [
            'label'    => 'Embroidery Digitizing',
            'url'      => home_url( '/design-digitizing/' ),
            'children' => [],
        ],
        [
            'label'    => 'Custom Patches',
            'url'      => home_url( '/custom-patches/' ),
            'children' => [],
        ],
        [
            'label'    => 'Service Areas',
            'url'      => home_url( '/service-areas/' ),
            'children' => [
                [ 'label' => 'Seattle',  'url' => home_url( '/service-areas/seattle/' ) ],
                [ 'label' => 'Bellevue', 'url' => home_url( '/service-areas/bellevue/' ) ],
                [ 'label' => 'Kirkland', 'url' => home_url( '/service-areas/kirkland/' ) ],
                [ 'label' => 'Renton',   'url' => home_url( '/service-areas/renton/' ) ],
                [ 'label' => 'Tacoma',   'url' => home_url( '/service-areas/tacoma/' ) ],
                [ 'label' => 'Redmond',  'url' => home_url( '/service-areas/redmond/' ) ],
                [ 'label' => 'Kent',     'url' => home_url( '/service-areas/kent/' ) ],
                [ 'label' => 'Bothell',  'url' => home_url( '/service-areas/bothell/' ) ],
            ],
        ],
        [
            'label'    => 'Fundraisers',
            'url'      => home_url( '/fundraisers/' ),
            'children' => [],
        ],
        [
            'label'    => 'Events',
            'url'      => home_url( '/events/' ),
            'children' => [],
        ],
        [
            'label'    => 'FAQ',
            'url'      => home_url( '/faq/' ),
            'children' => [],
        ],
        [
            'label'    => 'About',
            'url'      => home_url( '/about/' ),
            'children' => [],
        ],
    ];
}
