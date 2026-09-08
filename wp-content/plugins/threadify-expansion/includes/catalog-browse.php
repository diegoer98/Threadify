<?php
/**
 * Threadify Expansion — Filtered catalog browse pages (BETA DRAFTS)
 *
 * One page per catalog facet (/catalog/outerwear/, /catalog/caps/ …), each
 * listing every active style in that facet with client-side filters for
 * brand, colour family and size.
 *
 * Data comes from uploads/catalog-data/<facet>.txt — same shape as the
 * existing uploads/2026/07/catalog-*.txt files: a human header line, then
 * JSON on line 2. Product photos are SanMar CDN URLs, which is how the live
 * order builder already sources colour photos, so no product imagery is
 * committed here.
 *
 * Rendered through a the_content filter rather than baked in at activation.
 * Page bodies created by tse_maybe_create_page() are frozen the moment the
 * page exists (see CLAUDE.md), so anything data-driven has to render at
 * request time or it goes stale the first time the catalog changes.
 *
 * DRAFT STATUS: noindex and absent from every nav tree, like the rest of the
 * catalog work. Reachable only by URL or from /catalog/.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The facets that have a generated data file.
 * 'juniors' is deliberately absent — every style in it is discontinued.
 *
 * @return array<string,array{name:string,group:string}>
 */
function tse_browse_facets() {
	return [
		// Garment types.
		'sweatshirts'    => [ 'name' => 'Sweatshirts & Fleece', 'group' => 'garment' ],
		't-shirts'       => [ 'name' => 'T-Shirts',             'group' => 'garment' ],
		'outerwear'      => [ 'name' => 'Outerwear',            'group' => 'garment' ],
		'polos'          => [ 'name' => 'Polos & Knits',        'group' => 'garment' ],
		'bags'           => [ 'name' => 'Bags',                 'group' => 'garment' ],
		'caps'           => [ 'name' => 'Caps & Beanies',       'group' => 'garment' ],
		'activewear'     => [ 'name' => 'Activewear',           'group' => 'garment' ],
		'woven-shirts'   => [ 'name' => 'Woven & Dress Shirts', 'group' => 'garment' ],
		'workwear'       => [ 'name' => 'Workwear',             'group' => 'garment' ],
		'bottoms'        => [ 'name' => 'Bottoms',              'group' => 'garment' ],
		'accessories'    => [ 'name' => 'Accessories & Aprons', 'group' => 'garment' ],
		'hi-vis'         => [ 'name' => 'Hi-Vis & Protection',  'group' => 'garment' ],

		// Fits and size ranges.
		'womens'         => [ 'name' => "Women's",              'group' => 'fit' ],
		'youth'          => [ 'name' => 'Youth',                'group' => 'fit' ],
		'tall'           => [ 'name' => 'Tall',                 'group' => 'fit' ],
		'infant-toddler' => [ 'name' => 'Infant & Toddler',     'group' => 'fit' ],
	];
}

/** Colour families, in the order the filter row shows them. */
function tse_browse_colours() {
	return [
		'black'  => '#1A1A18', 'white'  => '#FFFFFF', 'grey'   => '#9A9A93',
		'blue'   => '#2F5B8C', 'green'  => '#2E4A35', 'red'    => '#9E2B25',
		'pink'   => '#D28AA0', 'purple' => '#5B4478', 'orange' => '#C4682B',
		'yellow' => '#C9A227', 'brown'  => '#6B4F35', 'other'  => 'transparent',
	];
}

function tse_browse_data_base() {
	$u = wp_upload_dir();
	return trailingslashit( $u['baseurl'] ) . 'catalog-data/';
}

// ── Page creation ───────────────────────────────────────────────────
register_activation_hook( TSE_DIR . 'threadify-expansion.php', 'tse_create_browse_pages' );

function tse_create_browse_pages() {

	if ( ! function_exists( 'tse_maybe_create_page' ) ) return;

	foreach ( tse_browse_facets() as $slug => $f ) {

		tse_maybe_create_page( [
			'slug'        => $slug,
			'title'       => $f['name'],
			'parent_slug' => 'catalog',
			'seo_title'   => $f['name'] . ' — Blank Apparel Catalog | Threadify',
			'meta_desc'   => 'Browse ' . strtolower( $f['name'] ) . ' available for custom embroidery, DTF printing and patches from Threadify in Federal Way, WA.',
			'content_fn'  => '__return_empty_string',
		] );

		$page = get_page_by_path( 'catalog/' . $slug );
		if ( $page ) {
			update_post_meta( $page->ID, '_tse_browse_facet', $slug );
			update_post_meta( $page->ID, '_tse_catalog_page', '1' );
		}
	}
}

/** The facet slug for the current request, or '' if this isn't a browse page. */
function tse_current_browse_facet() {
	if ( ! is_page() ) return '';
	$post = get_queried_object();
	if ( ! $post || ! isset( $post->ID ) ) return '';
	$f = get_post_meta( $post->ID, '_tse_browse_facet', true );
	return isset( tse_browse_facets()[ $f ] ) ? $f : '';
}

// ── Render at request time ──────────────────────────────────────────
add_filter( 'the_content', 'tse_browse_render_content', 5 );

function tse_browse_render_content( $content ) {

	$facet = tse_current_browse_facet();
	if ( ! $facet ) return $content;

	$facets = tse_browse_facets();
	$name   = $facets[ $facet ]['name'];

	$swatches = '';
	foreach ( tse_browse_colours() as $key => $hex ) {
		$style = $hex === 'transparent'
			? 'background:linear-gradient(135deg,#bbb 0 50%,#eee 50% 100%)'
			: 'background:' . $hex;
		$swatches .= '<button type="button" class="tbr-sw" data-colour="' . esc_attr( $key ) . '"
			style="' . esc_attr( $style ) . '" aria-pressed="false"
			aria-label="' . esc_attr( ucfirst( $key ) ) . '" title="' . esc_attr( ucfirst( $key ) ) . '"></button>';
	}

	ob_start();
	?>
<div class="tse-hero tbr-hero">
  <p class="tbr-crumb"><a href="<?php echo esc_url( home_url( '/catalog/' ) ); ?>">Catalog</a> &rsaquo; <?php echo esc_html( $name ); ?></p>
  <h1><?php echo esc_html( $name ); ?></h1>
  <p class="tbr-count" id="tbr-count">Loading&hellip;</p>
</div>

<div class="tse-section tbr-wrap"
     data-facet="<?php echo esc_attr( $facet ); ?>"
     data-src="<?php echo esc_url( tse_browse_data_base() . $facet . '.txt' ); ?>">

  <div class="tbr-filters">
    <!-- Hidden by the script on facets that only contain one gender, e.g. /catalog/womens/. -->
    <div class="tbr-frow" id="tbr-genrow" hidden>
      <span class="tbr-lbl">Gender</span>
      <div class="tbr-chips" id="tbr-genders"></div>
    </div>

    <div class="tbr-frow">
      <label class="tbr-lbl" for="tbr-brand">Brand</label>
      <select id="tbr-brand" class="tbr-select"><option value="">All brands</option></select>
    </div>

    <div class="tbr-frow">
      <span class="tbr-lbl">Colour</span>
      <div class="tbr-sws" id="tbr-colours"><?php echo $swatches; // phpcs:ignore ?></div>
    </div>

    <div class="tbr-frow">
      <span class="tbr-lbl">Size</span>
      <div class="tbr-chips" id="tbr-sizes"></div>
    </div>

    <button type="button" class="tbr-clear" id="tbr-clear" hidden>Clear filters</button>
  </div>

  <div class="tbr-grid" id="tbr-grid" aria-live="polite"></div>
  <p class="tbr-empty" id="tbr-empty" hidden>Nothing matches those filters. Try clearing one.</p>
  <div class="tbr-more"><button type="button" class="tse-btn" id="tbr-more" hidden>Show more</button></div>
</div>
	<?php
	return ob_get_clean() . $content;
}

// ── Assets ──────────────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'tse_browse_enqueue', 21 );

function tse_browse_enqueue() {
	if ( ! tse_current_browse_facet() ) return;
	wp_enqueue_style( 'tse-catalog', TSE_URL . 'css/catalog.css', [ 'tse-styles' ], TSE_VERSION );
	wp_enqueue_style( 'tse-browse',  TSE_URL . 'css/browse.css',  [ 'tse-catalog' ], TSE_VERSION );
}

add_action( 'wp_footer', 'tse_browse_script' );

function tse_browse_script() {
	if ( ! tse_current_browse_facet() ) return;
	?>
<script id="tse-browse-js">
(function () {
  "use strict";

  var wrap = document.querySelector(".tbr-wrap");
  if (!wrap) return;

  var PAGE = 48;                      // cards revealed per "Show more"
  var GENDERS = { m: "Men's & Unisex", w: "Women's", y: "Youth" };

  var all = [], shown = PAGE;
  var f = { brand: "", colours: [], sizes: [], genders: [] };

  var grid   = document.getElementById("tbr-grid");
  var count  = document.getElementById("tbr-count");
  var empty  = document.getElementById("tbr-empty");
  var more   = document.getElementById("tbr-more");
  var clear  = document.getElementById("tbr-clear");
  var brandS = document.getElementById("tbr-brand");
  var sizeC  = document.getElementById("tbr-sizes");
  var genC   = document.getElementById("tbr-genders");
  var genRow = document.getElementById("tbr-genrow");

  function matches(it) {
    if (f.brand && it.b !== f.brand) return false;
    if (f.genders.length && f.genders.indexOf(it.g) === -1) return false;
    if (f.colours.length && !f.colours.some(function (c) { return it.c.indexOf(c) > -1; })) return false;
    if (f.sizes.length && !f.sizes.some(function (s) { return it.z.indexOf(s) > -1; })) return false;
    return true;
  }

  function card(it) {
    var colours = it.c.length + (it.c.length === 1 ? " colour" : " colours");
    return '<article class="tbr-card">' +
             '<div class="tbr-shot"><img src="' + it.i + '" alt="" loading="lazy" decoding="async"></div>' +
             '<div class="tbr-body">' +
               '<span class="tbr-brand">' + it.b + '</span>' +
               '<h3 class="tbr-title">' + it.t + '</h3>' +
               '<span class="tbr-meta">' + it.a + '</span>' +
               '<span class="tbr-meta">' + colours + '</span>' +
             '</div>' +
           '</article>';
  }

  function render() {
    var hits = all.filter(matches);

    count.textContent = hits.length === all.length
      ? all.length + " styles"
      : hits.length + " of " + all.length + " styles";

    grid.innerHTML = hits.slice(0, shown).map(card).join("");
    empty.hidden = hits.length > 0;
    more.hidden  = hits.length <= shown;
    clear.hidden = !(f.brand || f.colours.length || f.sizes.length || f.genders.length);
  }

  function reset() { shown = PAGE; render(); }

  fetch(wrap.dataset.src)
    .then(function (r) { return r.text(); })
    .then(function (txt) {
      // Line 1 is the human header; the JSON payload is line 2.
      var data = JSON.parse(txt.slice(txt.indexOf("\n") + 1));
      all = data.items || [];

      // Only worth offering where the facet actually mixes genders — on
      // /catalog/womens/ or /catalog/youth/ every item is the same.
      var present = Object.keys(data.genders || {}).filter(function (k) {
        return data.genders[k] > 0 && GENDERS[k];
      });

      if (present.length > 1) {
        genRow.hidden = false;
        present.forEach(function (g) {
          var b = document.createElement("button");
          b.type = "button"; b.className = "tbr-chip";
          b.textContent = GENDERS[g] + " (" + data.genders[g] + ")";
          b.setAttribute("aria-pressed", "false");
          b.addEventListener("click", function () {
            var i = f.genders.indexOf(g);
            if (i > -1) { f.genders.splice(i, 1); b.setAttribute("aria-pressed", "false"); }
            else { f.genders.push(g); b.setAttribute("aria-pressed", "true"); }
            reset();
          });
          genC.appendChild(b);
        });
      }

      Object.keys(data.brands || {}).forEach(function (b) {
        var o = document.createElement("option");
        o.value = b; o.textContent = b + " (" + data.brands[b] + ")";
        brandS.appendChild(o);
      });

      Object.keys(data.sizes || {}).forEach(function (s) {
        var b = document.createElement("button");
        b.type = "button"; b.className = "tbr-chip"; b.textContent = s;
        b.setAttribute("aria-pressed", "false");
        b.addEventListener("click", function () {
          var i = f.sizes.indexOf(s);
          if (i > -1) { f.sizes.splice(i, 1); b.setAttribute("aria-pressed", "false"); }
          else { f.sizes.push(s); b.setAttribute("aria-pressed", "true"); }
          reset();
        });
        sizeC.appendChild(b);
      });

      render();
    })
    .catch(function () {
      count.textContent = "Catalog data could not be loaded.";
    });

  brandS.addEventListener("change", function () { f.brand = brandS.value; reset(); });

  document.getElementById("tbr-colours").addEventListener("click", function (e) {
    var b = e.target.closest(".tbr-sw");
    if (!b) return;
    var c = b.dataset.colour, i = f.colours.indexOf(c);
    if (i > -1) { f.colours.splice(i, 1); b.setAttribute("aria-pressed", "false"); }
    else { f.colours.push(c); b.setAttribute("aria-pressed", "true"); }
    reset();
  });

  more.addEventListener("click", function () { shown += PAGE; render(); });

  clear.addEventListener("click", function () {
    f = { brand: "", colours: [], sizes: [], genders: [] };
    brandS.value = "";
    wrap.querySelectorAll('[aria-pressed="true"]').forEach(function (el) {
      el.setAttribute("aria-pressed", "false");
    });
    reset();
  });
})();
</script>
	<?php
}
