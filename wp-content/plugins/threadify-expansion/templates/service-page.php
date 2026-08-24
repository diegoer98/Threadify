<?php
/**
 * Threadify Expansion — Standalone Service Page Template
 *
 * This file is returned by the template_include filter for all pages
 * created by this plugin. It renders a fully self-contained dark/gold
 * layout independent of the active theme.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$tse_title    = get_post_meta( get_the_ID(), '_tse_seo_title', true ) ?: get_the_title();
$tse_desc     = get_post_meta( get_the_ID(), '_tse_meta_desc', true ) ?: '';
$tse_content  = get_the_content();
$tse_home     = home_url('/');
$tse_contact  = home_url('/#contact');

/* Build breadcrumb */
$tse_ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
$tse_crumbs    = [];
foreach ( $tse_ancestors as $anc_id ) {
    $tse_crumbs[] = '<a href="' . get_permalink( $anc_id ) . '">' . get_the_title( $anc_id ) . '</a>';
}
$tse_crumbs[] = '<strong>' . get_the_title() . '</strong>';

/* Nav dropdowns data -- single canonical source, see includes/nav-tree.php */
$tse_nav = tse_nav_tree();

/* Current URL for active-state highlighting and canonical/schema use.
 * Query string is stripped: this URL feeds <link rel="canonical"> and the
 * JSON-LD `url` fields below, which must resolve to one clean address
 * regardless of tracking params (?utm_*, ?cb=, etc.) on the request. */
$tse_current_url = ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . strtok( $_SERVER['REQUEST_URI'], '?' );

/* Structured data: Service + BreadcrumbList (schema.org) */
$tse_schema_service = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    'name'        => $tse_title,
    'description' => $tse_desc ?: $tse_title,
    'url'         => $tse_current_url,
    'provider'    => [
        '@type'     => 'LocalBusiness',
        'name'      => 'Threadify Apparel',
        'telephone' => '+1-253-249-1545',
        'email'     => 'Orders@ThreadifyApparel.com',
        'address'   => [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'Federal Way',
            'addressRegion'   => 'WA',
            'addressCountry'  => 'US',
        ],
    ],
];

$tse_breadcrumb_items   = [ [ '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $tse_home ] ];
$tse_breadcrumb_position = 2;
foreach ( $tse_ancestors as $anc_id ) {
    $tse_breadcrumb_items[] = [
        '@type'    => 'ListItem',
        'position' => $tse_breadcrumb_position++,
        'name'     => get_the_title( $anc_id ),
        'item'     => get_permalink( $anc_id ),
    ];
}
$tse_breadcrumb_items[] = [
    '@type'    => 'ListItem',
    'position' => $tse_breadcrumb_position,
    'name'     => get_the_title(),
    'item'     => $tse_current_url,
];
$tse_schema_breadcrumb = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => $tse_breadcrumb_items,
];

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( $tse_title ); ?></title>
<?php if ( $tse_desc ) : ?>
<meta name="description" content="<?php echo esc_attr( $tse_desc ); ?>">
<meta property="og:description" content="<?php echo esc_attr( $tse_desc ); ?>">
<?php endif; ?>
<meta property="og:title" content="<?php echo esc_attr( $tse_title ); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo esc_url( $tse_current_url ); ?>">
<link rel="canonical" href="<?php echo esc_url( $tse_current_url ); ?>">
<script type="application/ld+json"><?php echo wp_json_encode( $tse_schema_service, JSON_UNESCAPED_SLASHES ); ?></script>
<script type="application/ld+json"><?php echo wp_json_encode( $tse_schema_breadcrumb, JSON_UNESCAPED_SLASHES ); ?></script>
<?php wp_head(); ?>
</head>
<body class="tse-body">

<a class="tse-skip" href="#tse-main">Skip to content</a>

<!-- ══ NAVIGATION ══════════════════════════════════ -->
<nav class="tse-nav" aria-label="Main navigation">
  <div class="tse-nav__inner">

    <a class="tse-nav__brand" href="<?php echo esc_url( $tse_home ); ?>">
      Thread<span>ify</span>
    </a>

    <button class="tse-nav__toggle" id="tse-toggle" aria-expanded="false" aria-controls="tse-menu" aria-label="Toggle menu">
      ☰
    </button>

    <ul class="tse-nav__list" id="tse-menu" role="menubar">
      <?php foreach ( $tse_nav as $item ) :
        $has_children = ! empty( $item['children'] );
        $is_active    = strpos( $tse_current_url, $item['url'] ) !== false;
      ?>
      <li role="none" class="<?php echo $is_active ? 'tse-active' : ''; ?>"<?php echo $has_children ? ' aria-haspopup="true"' : ''; ?>>
        <a href="<?php echo esc_url( $item['url'] ); ?>" role="menuitem"
           <?php echo $has_children ? 'aria-haspopup="true" aria-expanded="false"' : ''; ?>>
          <?php echo esc_html( $item['label'] ); ?><?php echo $has_children ? ' ▾' : ''; ?>
        </a>
        <?php if ( $has_children ) : ?>
        <ul role="menu" aria-label="<?php echo esc_attr( $item['label'] ); ?> submenu">
          <?php foreach ( $item['children'] as $child ) : ?>
          <li role="none">
            <a href="<?php echo esc_url( $child['url'] ); ?>" role="menuitem"><?php echo esc_html( $child['label'] ); ?></a>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>

      <li role="none">
        <a href="<?php echo esc_url( $tse_contact ); ?>" class="tse-btn" role="menuitem" style="margin-left:8px;padding:8px 18px;font-size:.8rem;">Get a Quote</a>
      </li>
    </ul>

  </div>
</nav>

<!-- ══ BREADCRUMB ══════════════════════════════════ -->
<?php if ( count( $tse_crumbs ) > 1 ) : ?>
<div class="tse-breadcrumb" aria-label="Breadcrumb">
  <a href="<?php echo esc_url( $tse_home ); ?>">Home</a>
  <span aria-hidden="true">›</span>
  <?php echo implode( '<span aria-hidden="true"> › </span>', $tse_crumbs ); ?>
</div>
<?php endif; ?>

<!-- ══ MAIN CONTENT ════════════════════════════════ -->
<main id="tse-main">
  <?php echo $tse_content; ?>
</main>

<!-- ══ FOOTER ══════════════════════════════════════ -->
<footer class="tse-footer">
  <div class="tse-footer__inner">

    <div>
      <h3>Services</h3>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/embroidery/')); ?>">Custom Embroidery</a></li>
        <li><a href="<?php echo esc_url(home_url('/embroidery/logo-embroidery/')); ?>">Logo Embroidery</a></li>
        <li><a href="<?php echo esc_url(home_url('/embroidery/embroidered-hats-caps/')); ?>">Embroidered Hats</a></li>
        <li><a href="<?php echo esc_url(home_url('/dtf-printing/')); ?>">DTF Printing</a></li>
        <li><a href="<?php echo esc_url(home_url('/dtf-printing/gang-sheets/')); ?>">Gang Sheets</a></li>
        <li><a href="<?php echo esc_url(home_url('/custom-patches/')); ?>">Custom Patches</a></li>
        <li><a href="<?php echo esc_url(home_url('/design-digitizing/')); ?>">Embroidery Digitizing</a></li>
      </ul>
    </div>

    <div>
      <h3>Apparel</h3>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/embroidery/embroidered-polo-shirts/')); ?>">Polo Shirts</a></li>
        <li><a href="<?php echo esc_url(home_url('/embroidery/embroidered-jackets/')); ?>">Jackets</a></li>
        <li><a href="<?php echo esc_url(home_url('/embroidery/embroidered-beanies/')); ?>">Beanies</a></li>
        <li><a href="<?php echo esc_url(home_url('/embroidery/embroidered-hoodies/')); ?>">Hoodies</a></li>
        <li><a href="<?php echo esc_url(home_url('/dtf-printing/custom-t-shirts/')); ?>">Custom T-Shirts</a></li>
        <li><a href="<?php echo esc_url(home_url('/dtf-printing/dtf-for-teams/')); ?>">Team Orders</a></li>
      </ul>
    </div>

    <div>
      <h3>Service Areas</h3>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/service-areas/seattle/')); ?>">Seattle</a></li>
        <li><a href="<?php echo esc_url(home_url('/service-areas/bellevue/')); ?>">Bellevue</a></li>
        <li><a href="<?php echo esc_url(home_url('/service-areas/kirkland/')); ?>">Kirkland</a></li>
        <li><a href="<?php echo esc_url(home_url('/service-areas/tacoma/')); ?>">Tacoma</a></li>
        <li><a href="<?php echo esc_url(home_url('/service-areas/renton/')); ?>">Renton</a></li>
        <li><a href="<?php echo esc_url(home_url('/service-areas/')); ?>">All Areas →</a></li>
      </ul>
    </div>

    <div>
      <h3>Contact</h3>
      <ul>
        <li><a href="mailto:Orders@ThreadifyApparel.com">Orders@ThreadifyApparel.com</a></li>
        <li><a href="tel:+12532491545">(253) 249-1545</a></li>
        <li><a href="<?php echo esc_url( $tse_contact ); ?>">Request a Quote</a></li>
        <li><a href="<?php echo esc_url(home_url('/faq/')); ?>">FAQ</a></li>
        <li><a href="<?php echo esc_url(home_url('/about/')); ?>">About Us</a></li>
      </ul>
    </div>

  </div>

  <div class="tse-footer__bottom">
    <a class="tse-footer__brand" href="<?php echo esc_url( $tse_home ); ?>">Threadify</a>
    <span>&copy; <?php echo date('Y'); ?> Threadify Apparel. All rights reserved. Federal Way, WA.</span>
  </div>
</footer>

<script>
/* ── Service page nav dropdowns (elementFromPoint hover fix) ─── */
(function() {
  var closeTimers = new WeakMap();

  document.querySelectorAll('.tse-nav__list > li').forEach(function(li) {
    if (!li.querySelector('ul')) return; // no dropdown, skip

    document.addEventListener('mousemove', function(e) {
      var el = document.elementFromPoint(e.clientX, e.clientY);
      if (el && li.contains(el)) {
        // Cursor is over this li or its dropdown — open and cancel any close
        clearTimeout(closeTimers.get(li));
        li.classList.add('tse-dd-open');
      } else if (li.classList.contains('tse-dd-open')) {
        // Cursor left — schedule close
        if (!closeTimers.get(li)) {
          closeTimers.set(li, setTimeout(function() {
            li.classList.remove('tse-dd-open');
            closeTimers.set(li, null);
          }, 300));
        }
      }
    });

    // Mobile: tap to toggle
    li.querySelector('a').addEventListener('click', function(e) {
      if (window.innerWidth < 900) {
        e.preventDefault();
        li.classList.toggle('tse-dd-open');
      }
    });
  });
})();

/* ── Mobile nav toggle ───────────────────────── */
(function () {
  var btn  = document.getElementById('tse-toggle');
  var menu = document.getElementById('tse-menu');
  if (!btn || !menu) return;

  btn.addEventListener('click', function () {
    var open = menu.classList.toggle('tse-open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    btn.textContent = open ? '✕' : '☰';
  });

  // Close on outside click
  document.addEventListener('click', function (e) {
    if (!btn.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.remove('tse-open');
      btn.setAttribute('aria-expanded', 'false');
      btn.textContent = '☰';
    }
  });
})();

/* ── FAQ accordion ───────────────────────────── */
(function () {
  document.querySelectorAll('.tse-faq__q').forEach(function (q) {
    q.addEventListener('click', function () {
      var item = this.closest('.tse-faq__item');
      var wasOpen = item.classList.contains('tse-open');

      // Close all siblings
      var parent = item.parentElement;
      parent.querySelectorAll('.tse-faq__item.tse-open').forEach(function (open) {
        if (open !== item) open.classList.remove('tse-open');
      });

      item.classList.toggle('tse-open', !wasOpen);
    });
  });
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
