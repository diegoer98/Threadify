<?php
/**
 * Plugin Name: Threadify Site Expansion
 * Plugin URI: https://threadifyapparel.com
 * Description: Adds SEO-rich service pages, FAQ, service areas, and full navigation for embroidery and DTF printing — keeps the dark/gold Threadify aesthetic.
 * Version: 1.0.0
 * Author: Threadify
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'TSE_VERSION', '1.1.1' );
define( 'TSE_DIR',     plugin_dir_path( __FILE__ ) );
define( 'TSE_URL',     plugin_dir_url( __FILE__ ) );

require_once TSE_DIR . 'includes/page-content.php';
require_once TSE_DIR . 'includes/create-pages.php';
require_once TSE_DIR . 'includes/create-menu.php';

// BETA — /brands/. Self-contained; registers its own activation hook, styles
// and meta. Remove this line to switch the page off entirely.
require_once TSE_DIR . 'includes/brands.php';

// ── Lifecycle ──────────────────────────────────────────────────────
register_activation_hook( __FILE__, 'tse_activate' );

function tse_activate() {
    tse_create_all_pages();
    tse_create_city_pages(); // city pages need /service-areas/ parent to exist first
    tse_create_menu();
    flush_rewrite_rules();
}

// ── Styles ─────────────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'tse_enqueue' );
function tse_enqueue() {
    if ( tse_is_service_page() ) {
        wp_enqueue_style( 'tse-styles', TSE_URL . 'css/styles.css', [], TSE_VERSION );
    }
}

// ── Template override ──────────────────────────────────────────────
add_filter( 'template_include', 'tse_template_include', 99 );
function tse_template_include( $template ) {
    if ( tse_is_service_page() ) {
        $t = TSE_DIR . 'templates/service-page.php';
        if ( file_exists( $t ) ) {
            // service-page.php prints its own <title>/meta/OG tags before calling
            // wp_head(); without this, WP core's title-tag renderer fires a second
            // time inside wp_head() and duplicates the <title> element.
            remove_action( 'wp_head', '_wp_render_title_tag', 1 );
            return $t;
        }
    }
    return $template;
}

// ── SEO title ──────────────────────────────────────────────────────
add_filter( 'document_title_parts', 'tse_document_title', 20 );
function tse_document_title( $parts ) {
    if ( tse_is_service_page() ) {
        $post = get_queried_object();
        $seo  = get_post_meta( $post->ID, '_tse_seo_title', true );
        if ( $seo ) {
            $parts['title'] = $seo;
            unset( $parts['tagline'] );
            $parts['site'] = 'Threadify';
        }
    }
    return $parts;
}

// ── Dequeue unused assets on service pages ───────────────────────────
// These pages have zero storefront and their own inline vanilla-JS nav/FAQ
// scripts (no jQuery dependency), so the default WooCommerce frontend
// bundle is dead weight. Priority 100 so it runs after WooCommerce's own
// wp_enqueue_scripts registration (default priority 10).
add_action( 'wp_enqueue_scripts', 'tse_dequeue_unused_assets', 100 );
function tse_dequeue_unused_assets() {
    if ( ! tse_is_service_page() ) return;

    foreach ( [ 'wc-add-to-cart', 'jquery-blockui', 'js-cookie', 'woocommerce' ] as $handle ) {
        wp_dequeue_script( $handle );
        wp_deregister_script( $handle );
    }

    foreach ( [ 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen', 'wc-blocks-style' ] as $handle ) {
        wp_dequeue_style( $handle );
        wp_deregister_style( $handle );
    }
}

// ── Helper ─────────────────────────────────────────────────────────
function tse_is_service_page() {
    if ( ! is_page() ) return false;
    $post = get_queried_object();
    if ( ! $post || ! isset( $post->ID ) ) return false;
    return get_post_meta( $post->ID, '_tse_service_page', true ) === '1';
}

// ── Homepage nav injection ─────────────────────────────────────────
add_action( 'wp_footer', 'tse_inject_homepage_nav' );
function tse_inject_homepage_nav() {
    if ( tse_is_service_page() ) return;
    if ( ! is_front_page() && ! is_home() ) return;

    $base = home_url('/');
    ?>
<style id="tse-nav-inject-css">
.tse-dd-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.tse-dd-trigger {
    cursor: pointer;
    white-space: nowrap;
}
.tse-dd-menu {
    visibility: hidden;
    opacity: 0;
    pointer-events: none;
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #0f0f0f;
    border: 1px solid #333;
    border-radius: 6px;
    min-width: 210px;
    padding: 10px 0 6px;
    z-index: 99999;
    box-shadow: 0 10px 40px rgba(0,0,0,.8);
    list-style: none;
    margin: 0;
    transition: opacity .18s, visibility .18s;
}
.tse-dd-wrap.tse-open .tse-dd-menu {
    visibility: visible;
    opacity: 1;
    pointer-events: auto;
}
.tse-dd-menu li { margin: 0; padding: 0; }
.tse-dd-menu a {
    display: block;
    padding: 9px 18px;
    color: #e0e0e0 !important;
    font-size: .875rem;
    white-space: nowrap;
    transition: color .12s, background .12s;
    text-decoration: none !important;
    font-weight: 400;
}
.tse-dd-menu a:hover {
    color: #c9a84c !important;
    background: rgba(201,168,76,.08);
}
.tse-dd-sep {
    height: 1px;
    background: #2a2a2a;
    margin: 5px 0;
}
.tse-dd-caret {
    color: #c9a84c;
    font-size: .7em;
    margin-left: 4px;
    display: inline-block;
    transition: transform .2s;
}
.tse-dd-wrap.tse-open .tse-dd-caret {
    transform: rotate(180deg);
}
</style>
<script id="tse-nav-inject-js">
(function() {

    var NAV_ITEMS = [
        {
            label: 'Embroidery',
            url:   '<?php echo esc_url($base . "embroidery/"); ?>',
            children: [
                { label: 'All Embroidery Services',   url: '<?php echo esc_url($base . "embroidery/"); ?>' },
                { sep: true },
                { label: 'Logo Embroidery',           url: '<?php echo esc_url($base . "embroidery/logo-embroidery/"); ?>' },
                { label: 'Embroidered Polo Shirts',   url: '<?php echo esc_url($base . "embroidery/embroidered-polo-shirts/"); ?>' },
                { label: 'Embroidered Jackets',       url: '<?php echo esc_url($base . "embroidery/embroidered-jackets/"); ?>' },
                { label: 'Embroidered Hats & Caps',   url: '<?php echo esc_url($base . "embroidery/embroidered-hats-caps/"); ?>' },
                { label: 'Embroidered Beanies',       url: '<?php echo esc_url($base . "embroidery/embroidered-beanies/"); ?>' },
                { label: 'Embroidered Hoodies',       url: '<?php echo esc_url($base . "embroidery/embroidered-hoodies/"); ?>' },
            ]
        },
        {
            label: 'DTF Printing',
            url:   '<?php echo esc_url($base . "dtf-printing/"); ?>',
            children: [
                { label: 'All DTF Printing',          url: '<?php echo esc_url($base . "dtf-printing/"); ?>' },
                { sep: true },
                { label: 'Custom T-Shirts',           url: '<?php echo esc_url($base . "dtf-printing/custom-t-shirts/"); ?>' },
                { label: 'DTF for Teams & Groups',    url: '<?php echo esc_url($base . "dtf-printing/dtf-for-teams/"); ?>' },
                { label: 'DTF Gang Sheets',           url: '<?php echo esc_url($base . "dtf-printing/gang-sheets/"); ?>' },
            ]
        },
        {
            label: 'Patches',
            url:   '<?php echo esc_url($base . "custom-patches/"); ?>',
            children: []
        },
        {
            label: 'Digitizing',
            url:   '<?php echo esc_url($base . "design-digitizing/"); ?>',
            children: []
        },
        {
            label: 'Service Areas',
            url:   '<?php echo esc_url($base . "service-areas/"); ?>',
            children: [
                { label: 'All Service Areas',  url: '<?php echo esc_url($base . "service-areas/"); ?>' },
                { sep: true },
                { label: 'Seattle',            url: '<?php echo esc_url($base . "service-areas/seattle/"); ?>' },
                { label: 'Bellevue',           url: '<?php echo esc_url($base . "service-areas/bellevue/"); ?>' },
                { label: 'Kirkland',           url: '<?php echo esc_url($base . "service-areas/kirkland/"); ?>' },
                { label: 'Renton',             url: '<?php echo esc_url($base . "service-areas/renton/"); ?>' },
                { label: 'Tacoma',             url: '<?php echo esc_url($base . "service-areas/tacoma/"); ?>' },
                { label: 'Redmond',            url: '<?php echo esc_url($base . "service-areas/redmond/"); ?>' },
                { label: 'Kent',               url: '<?php echo esc_url($base . "service-areas/kent/"); ?>' },
                { label: 'Bothell',            url: '<?php echo esc_url($base . "service-areas/bothell/"); ?>' },
            ]
        },
        {
            label: 'FAQ',
            url:   '<?php echo esc_url($base . "faq/"); ?>',
            children: []
        },
        {
            label: 'About',
            url:   '<?php echo esc_url($base . "about/"); ?>',
            children: []
        },
    ];

    function buildItem(item) {
        var wrap = document.createElement('span');
        wrap.className = 'tse-dd-wrap';

        var a = document.createElement('a');
        a.className = 'tse-dd-trigger';
        a.href = item.url;

        var hasChildren = item.children && item.children.length > 0;

        if (hasChildren) {
            a.innerHTML = item.label + '<span class="tse-dd-caret">▾</span>';
        } else {
            a.textContent = item.label;
        }

        // Match existing nav link styles by cloning from an existing link
        wrap.appendChild(a);

        if (hasChildren) {
            var menu = document.createElement('ul');
            menu.className = 'tse-dd-menu';
            item.children.forEach(function(child) {
                var li = document.createElement('li');
                if (child.sep) {
                    var sep = document.createElement('div');
                    sep.className = 'tse-dd-sep';
                    li.appendChild(sep);
                } else {
                    var ca = document.createElement('a');
                    ca.href = child.url;
                    ca.textContent = child.label;
                    li.appendChild(ca);
                }
                menu.appendChild(li);
            });
            wrap.appendChild(menu);

            // Bulletproof hover: track actual cursor position via mousemove
            // mouseleave fires incorrectly when entering absolute-positioned children,
            // so we use document.elementFromPoint to know the real truth.
            var closeTimer = null;

            function openMenu() {
                clearTimeout(closeTimer);
                wrap.classList.add('tse-open');
            }

            function startCloseTimer() {
                clearTimeout(closeTimer);
                closeTimer = setTimeout(function() {
                    wrap.classList.remove('tse-open');
                }, 300);
            }

            // Open on hover
            wrap.addEventListener('mouseenter', openMenu);

            // On mousemove across the document, if the menu is open,
            // check whether the cursor is genuinely over this wrap (or its dropdown).
            // Close only when the cursor is truly elsewhere.
            document.addEventListener('mousemove', function(e) {
                if (!wrap.classList.contains('tse-open')) return;
                var el = document.elementFromPoint(e.clientX, e.clientY);
                if (el && wrap.contains(el)) {
                    // Cursor is over the trigger or a dropdown item — stay open
                    clearTimeout(closeTimer);
                } else {
                    // Cursor left the whole dropdown — schedule close
                    startCloseTimer();
                }
            });

            // Mobile tap toggle
            a.addEventListener('click', function(e) {
                if (window.innerWidth < 900) {
                    e.preventDefault();
                    wrap.classList.toggle('tse-open');
                }
            });
        }

        return wrap;
    }

    function injectNav() {
        // Find and remove the existing "Services" link
        var servicesLink = null;
        document.querySelectorAll('a').forEach(function(a) {
            if (a.textContent.trim() === 'Services' && a.getAttribute('href') === '#services') {
                servicesLink = a;
            }
        });

        if (!servicesLink) return;

        var insertAfter = servicesLink.parentNode;

        // Remove the Services link
        servicesLink.remove();

        // Insert new items in place using a DocumentFragment
        var frag = document.createDocumentFragment();
        NAV_ITEMS.forEach(function(item) {
            // Copy the styling approach from existing nav links where possible
            var wrapper = document.createElement('li');
            wrapper.style.cssText = 'display:inline-flex;align-items:center;list-style:none;';
            var built = buildItem(item);

            // Try to copy computed styles from a sibling nav link
            var siblingLink = insertAfter.parentNode && insertAfter.parentNode.querySelector('a');
            if (siblingLink) {
                var cs = window.getComputedStyle(siblingLink);
                built.querySelector('a').style.cssText =
                    'color:' + cs.color +
                    ';font-family:' + cs.fontFamily +
                    ';font-size:' + cs.fontSize +
                    ';font-weight:' + cs.fontWeight +
                    ';letter-spacing:' + cs.letterSpacing +
                    ';text-transform:' + cs.textTransform +
                    ';padding:' + cs.padding +
                    ';text-decoration:none;';
            }

            wrapper.appendChild(built);
            frag.appendChild(wrapper);
        });

        // Insert the fragment where Services was
        insertAfter.parentNode.insertBefore(frag, insertAfter);
        insertAfter.remove();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectNav);
    } else {
        injectNav();
    }
})();
</script>
    <?php
}
