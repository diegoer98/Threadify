<?php
/**
 * Threadify Expansion — Page Creation
 * Called on plugin activation.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function tse_create_all_pages() {

    $pages = tse_page_definitions();

    foreach ( $pages as $def ) {
        tse_maybe_create_page( $def );
    }

    // Flush once after all pages are created.
    flush_rewrite_rules();
}

/**
 * Full list of pages to create.
 * Keys: slug, title, parent_slug, seo_title, meta_desc, content_fn, content_arg
 */
function tse_page_definitions() {
    return [

        /* ── EMBROIDERY PARENT ─────────────────────── */
        [
            'slug'        => 'embroidery',
            'title'       => 'Custom Embroidery',
            'parent_slug' => '',
            'seo_title'   => 'Custom Embroidery Services in Seattle & Puget Sound | Threadify',
            'meta_desc'   => 'Premium custom embroidery for businesses and individuals in Seattle. Polos, jackets, hats, beanies & more. Low minimums, fast turnaround. Get a free quote.',
            'content_fn'  => 'tse_content_embroidery_main',
        ],
        [
            'slug'        => 'logo-embroidery',
            'title'       => 'Logo Embroidery',
            'parent_slug' => 'embroidery',
            'seo_title'   => 'Logo Embroidery Services — Custom Branded Apparel | Threadify',
            'meta_desc'   => 'Turn your logo into precision embroidery. Threadify digitizes and stitches your brand onto polos, jackets, hats, and more. Low minimums, no setup fees.',
            'content_fn'  => 'tse_content_logo_embroidery',
        ],
        [
            'slug'        => 'embroidered-polo-shirts',
            'title'       => 'Embroidered Polo Shirts',
            'parent_slug' => 'embroidery',
            'seo_title'   => 'Custom Embroidered Polo Shirts for Businesses | Threadify',
            'meta_desc'   => 'Custom embroidered polo shirts for uniforms, corporate apparel & teams. Quality brands, precise stitching, low minimums. Order online from Threadify in Seattle.',
            'content_fn'  => 'tse_content_polo_shirts',
        ],
        [
            'slug'        => 'embroidered-jackets',
            'title'       => 'Embroidered Jackets',
            'parent_slug' => 'embroidery',
            'seo_title'   => 'Custom Embroidered Jackets & Outerwear | Threadify Apparel',
            'meta_desc'   => 'Custom embroidered jackets for teams, crews, and corporate gifting. Fleece, softshell, puffer & more. Based in Seattle — ships to all of Washington.',
            'content_fn'  => 'tse_content_jackets',
        ],
        [
            'slug'        => 'embroidered-hats-caps',
            'title'       => 'Embroidered Hats & Caps',
            'parent_slug' => 'embroidery',
            'seo_title'   => 'Custom Embroidered Hats & Caps | Structured & Unstructured | Threadify',
            'meta_desc'   => 'Custom embroidered hats and caps for brands, teams, and events. Structured, unstructured, snapback, dad hat & trucker styles. Low minimums from Threadify.',
            'content_fn'  => 'tse_content_hats',
        ],
        [
            'slug'        => 'embroidered-beanies',
            'title'       => 'Embroidered Beanies',
            'parent_slug' => 'embroidery',
            'seo_title'   => 'Custom Embroidered Beanies | Wholesale & Retail | Threadify',
            'meta_desc'   => 'Custom embroidered beanies for teams, companies & giveaways. Cuffed, pom-pom, and slouch styles available. Based in Seattle, WA. Get a free quote.',
            'content_fn'  => 'tse_content_beanies',
        ],
        [
            'slug'        => 'embroidered-hoodies',
            'title'       => 'Embroidered Hoodies',
            'parent_slug' => 'embroidery',
            'seo_title'   => 'Custom Embroidered Hoodies & Sweatshirts | Threadify Apparel',
            'meta_desc'   => 'Custom embroidered hoodies, zip-ups, and crewneck sweatshirts. Team spirit, staff uniforms, or branded merch. Order from Threadify in Seattle.',
            'content_fn'  => 'tse_content_hoodies',
        ],

        /* ── DTF PARENT ────────────────────────────── */
        [
            'slug'        => 'dtf-printing',
            'title'       => 'DTF Printing',
            'parent_slug' => '',
            'seo_title'   => 'DTF Printing Services in Seattle & Puget Sound | Threadify',
            'meta_desc'   => 'High-quality DTF (Direct-to-Film) printing for t-shirts, hoodies, and apparel. Full color, no minimums, fast turnaround. Serving Seattle and all of Washington.',
            'content_fn'  => 'tse_content_dtf_main',
        ],
        [
            'slug'        => 'custom-t-shirts',
            'title'       => 'Custom T-Shirts',
            'parent_slug' => 'dtf-printing',
            'seo_title'   => 'Custom Printed T-Shirts — DTF Printing | Threadify Apparel',
            'meta_desc'   => 'Custom full-color t-shirts via DTF printing. No minimums, no color limits, vibrant and wash-durable prints. Get a free quote from Threadify in Seattle.',
            'content_fn'  => 'tse_content_dtf_tshirts',
        ],
        [
            'slug'        => 'dtf-for-teams',
            'title'       => 'DTF Printing for Teams & Groups',
            'parent_slug' => 'dtf-printing',
            'seo_title'   => 'DTF Printing for Sports Teams & Groups | Threadify Apparel',
            'meta_desc'   => 'Fast, affordable DTF printing for sports teams, clubs, and group orders. Full-color prints, mixed sizes, quick turnaround. Serving Seattle & beyond.',
            'content_fn'  => 'tse_content_dtf_teams',
        ],
        [
            'slug'        => 'gang-sheets',
            'title'       => 'DTF Gang Sheets',
            'parent_slug' => 'dtf-printing',
            'seo_title'   => 'DTF Gang Sheets — Custom Heat Transfer Printing | Threadify',
            'meta_desc'   => 'Order custom DTF gang sheets for your own application. Maximize your print area, save on costs. Perfect for decorators and small businesses. Threadify, Seattle WA.',
            'content_fn'  => 'tse_content_gang_sheets',
        ],

        /* ── OTHER SERVICES ─────────────────────────── */
        [
            'slug'        => 'design-digitizing',
            'title'       => 'Embroidery Digitizing',
            'parent_slug' => '',
            'seo_title'   => 'Embroidery Digitizing Services | Convert Logo to Embroidery File | Threadify',
            'meta_desc'   => 'Professional embroidery digitizing — convert any logo or artwork into an embroidery file. Fast, affordable, and precise. Order online from Threadify in Seattle.',
            'content_fn'  => 'tse_content_digitizing',
        ],
        [
            'slug'        => 'custom-patches',
            'title'       => 'Custom Patches',
            'parent_slug' => '',
            'seo_title'   => 'Custom Embroidered Patches | Iron-On, Sew-On & Velcro | Threadify',
            'meta_desc'   => 'Custom embroidered patches with iron-on, sew-on, or Velcro backing. Merrowed or laser-cut edges. Perfect for uniforms, jackets, and hats. Order from Threadify.',
            'content_fn'  => 'tse_content_patches',
        ],

        /* ── INFO PAGES ─────────────────────────────── */
        [
            'slug'        => 'faq',
            'title'       => 'FAQ — Frequently Asked Questions',
            'parent_slug' => '',
            'seo_title'   => 'Embroidery & DTF Printing FAQ | Threadify Apparel Seattle',
            'meta_desc'   => 'Answers to common questions about ordering custom embroidery and DTF printing from Threadify. Minimums, turnaround, file formats, pricing, and more.',
            'content_fn'  => 'tse_content_faq',
        ],
        [
            'slug'        => 'about',
            'title'       => 'About Threadify',
            'parent_slug' => '',
            'seo_title'   => 'About Threadify — Custom Embroidery & DTF Printing | Seattle WA',
            'meta_desc'   => 'Threadify is a Seattle-based custom apparel studio specializing in embroidery and DTF printing. Learn about our story, values, and what makes us different.',
            'content_fn'  => 'tse_content_about',
        ],

        /* ── SERVICE AREAS ──────────────────────────── */
        [
            'slug'        => 'service-areas',
            'title'       => 'Service Areas',
            'parent_slug' => '',
            'seo_title'   => 'Embroidery & DTF Printing Service Areas — Greater Seattle | Threadify',
            'meta_desc'   => 'Threadify serves Seattle, Bellevue, Tacoma, Kirkland, Renton, Redmond, Kent, Bothell and all of the greater Puget Sound region.',
            'content_fn'  => 'tse_content_service_areas',
        ],
    ];
}

/**
 * City service area page definitions (created separately).
 */
function tse_city_pages() {
    return [
        [ 'slug' => 'seattle',  'name' => 'Seattle'  ],
        [ 'slug' => 'bellevue', 'name' => 'Bellevue' ],
        [ 'slug' => 'kirkland', 'name' => 'Kirkland' ],
        [ 'slug' => 'renton',   'name' => 'Renton'   ],
        [ 'slug' => 'tacoma',   'name' => 'Tacoma'   ],
        [ 'slug' => 'redmond',  'name' => 'Redmond'  ],
        [ 'slug' => 'kent',     'name' => 'Kent'     ],
        [ 'slug' => 'bothell',  'name' => 'Bothell'  ],
    ];
}

/**
 * Create a page if it doesn't already exist; set all required meta.
 */
function tse_maybe_create_page( $def ) {
    $slug        = $def['slug'];
    $parent_slug = $def['parent_slug'] ?? '';
    $parent_id   = 0;

    if ( $parent_slug ) {
        $parent = get_page_by_path( $parent_slug );
        if ( $parent ) {
            $parent_id = $parent->ID;
        }
    }

    // Full path including parent.
    $full_path = $parent_slug ? $parent_slug . '/' . $slug : $slug;
    $existing  = get_page_by_path( $full_path );

    if ( $existing ) {
        $page_id = $existing->ID;
    } else {
        $content = '';
        if ( ! empty( $def['content_fn'] ) && function_exists( $def['content_fn'] ) ) {
            $content = call_user_func( $def['content_fn'] );
        }

        $page_id = wp_insert_post( [
            'post_title'     => $def['title'],
            'post_name'      => $slug,
            'post_content'   => $content,
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_parent'    => $parent_id,
            'comment_status' => 'closed',
        ] );
    }

    if ( $page_id && ! is_wp_error( $page_id ) ) {
        update_post_meta( $page_id, '_tse_service_page', '1' );
        if ( ! empty( $def['seo_title'] ) ) {
            update_post_meta( $page_id, '_tse_seo_title', $def['seo_title'] );
        }
        if ( ! empty( $def['meta_desc'] ) ) {
            update_post_meta( $page_id, '_tse_meta_desc', $def['meta_desc'] );
        }
    }

    return $page_id;
}

/**
 * Create city service area child pages under /service-areas/.
 */
function tse_create_city_pages() {
    $parent = get_page_by_path( 'service-areas' );
    if ( ! $parent ) return;

    foreach ( tse_city_pages() as $city ) {
        $full_path = 'service-areas/' . $city['slug'];
        $existing  = get_page_by_path( $full_path );

        if ( $existing ) {
            $page_id = $existing->ID;
        } else {
            $content = tse_content_service_area_city( $city['name'] );
            $page_id = wp_insert_post( [
                'post_title'     => 'Custom Embroidery & DTF Printing in ' . $city['name'],
                'post_name'      => $city['slug'],
                'post_content'   => $content,
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_parent'    => $parent->ID,
                'comment_status' => 'closed',
            ] );
        }

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            update_post_meta( $page_id, '_tse_service_page', '1' );
            update_post_meta( $page_id, '_tse_seo_title',
                'Custom Embroidery & DTF Printing in ' . $city['name'] . ' WA | Threadify' );
            update_post_meta( $page_id, '_tse_meta_desc',
                'Threadify provides custom embroidery and DTF printing in ' . $city['name'] . ', WA. Fast turnaround, honest pricing. Request a free quote today.' );
        }
    }
}
