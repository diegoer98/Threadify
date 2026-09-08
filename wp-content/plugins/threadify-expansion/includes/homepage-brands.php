<?php
/**
 * Threadify Expansion — Homepage patches (BETA, off by default)
 *
 * Four homepage changes, all applied by DOM surgery on wp_footer:
 *
 *   1. a brand carousel inserted between the hero and #services,
 *   2. #industries removed (its content now lives on the catalog page),
 *   3. the nav's "Shop" tab relabelled "Catalog" and pointed at that page,
 *   4. a "Brands" link added to the footer Explore list, between Services
 *      and How It Works.
 *
 * WHY JAVASCRIPT: the homepage is not rendered by any file in this repo. Its
 * markup is raw post_content in the database, echoed by code-snippets snippet
 * id=6, so a template edit cannot touch it. The alternative is a gated
 * db-content/<TICKET>/ script applied over WP-CLI.
 *
 * WHY NOT wp_footer: snippet id=6 echoes a complete document and exits on
 * template_redirect, so wp_footer() never runs on the homepage — fetching the
 * live page shows no wp-includes assets and no footer output at all, and the
 * document simply ends "</div></body></html>". tse_inject_homepage_nav() hooks
 * wp_footer and is therefore dead on the homepage today, which is worth knowing
 * before trusting that pattern. This file instead opens an output buffer ahead
 * of the snippet and rewrites the HTML on its way out, which works whether or
 * not the snippet exits early (PHP flushes buffers, running their callbacks, at
 * shutdown).
 *
 * ─────────────────────────────────────────────────────────────────────
 * THIS IS OFF BY DEFAULT AND MUST STAY OFF UNTIL THE CATALOG PAGE EXISTS.
 *
 * Unlike /brands/, which nobody reaches without typing the URL, this file
 * rewrites the live homepage the moment it is switched on. Step 2 deletes the
 * only entry point to the order builder from the homepage, so enabling this
 * before the catalog page is live strands customers.
 *
 * To switch on: change TFB_HOMEPAGE_PATCH below to true.
 * ─────────────────────────────────────────────────────────────────────
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Master switch. Leave false until the catalog page ships. */
if ( ! defined( 'TFB_HOMEPAGE_PATCH' ) ) {
	define( 'TFB_HOMEPAGE_PATCH', false );
}

/**
 * Where the "Catalog" tab points.
 *
 * Defaults to /shop/ because the shop-by-industry content has already been
 * ported there verbatim — same eyebrow, headline, #indTabs markup and all 13
 * industry tabs. Rebuilding it under /catalog/ would duplicate that work, so
 * this points at the existing page instead. Change the value here if the page
 * is ever renamed.
 */
if ( ! defined( 'TFB_CATALOG_URL' ) ) {
	define( 'TFB_CATALOG_URL', '/shop/' );
}

/**
 * Every brand, ordered by public recognition — the names a customer is most
 * likely to know come first, so page one of the carousel does the most work.
 * Files resolve against uploads/brands/ and are shared with the /brands/ page.
 *
 * @return array<int,array{name:string,file:string}>
 */
function tse_homepage_brand_order() {

	$order = [
		// Page 1 — household names.
		'Nike Golf', 'Carhartt', 'The North Face', 'Champion', 'Eddie Bauer',
		'New Era', 'Brooks Brothers', 'Tommy Bahama', 'BELLA+CANVAS', 'Gildan',

		// Page 2 — widely known in apparel and decorating.
		'Comfort Colors', 'Next Level', 'Fruit of the Loom', 'Jerzees', 'TravisMathew',
		'Cotopaxi', 'OGIO', 'Richardson', 'Port Authority', 'District',

		// Page 3 — trade staples.
		'Sport-Tek', 'Port & Company', 'Red Kap', 'CornerStone', 'Alternative',
		'Outdoor Research', 'tentree', 'Bulwark', 'Anvil', 'Allmade',

		// Page 4 — specialist lines.
		'Stanley/Stella', 'Russell Outdoors', 'Mercer+Mettle', 'Red House', 'Rabbit Skins',
		'Spacecraft', 'WonderWink', 'Volunteer Knitwear', 'A4',
	];

	// Flatten the grouped registry to name => file.
	$files = [];
	foreach ( tse_brand_groups() as $group ) {
		foreach ( $group['brands'] as $name => $file ) {
			$files[ $name ] = $file;
		}
	}

	$out = [];
	foreach ( $order as $name ) {
		if ( isset( $files[ $name ] ) ) {
			$out[] = [ 'name' => $name, 'file' => $files[ $name ] ];
		}
	}

	return $out;
}

// ── Injection ───────────────────────────────────────────────────────
// Priority 0 so the buffer opens before snippet id=6 echoes the page.
add_action( 'template_redirect', 'tse_homepage_buffer', 0 );

function tse_homepage_buffer() {
	if ( ! TFB_HOMEPAGE_PATCH ) return;
	if ( ! is_front_page() && ! is_home() ) return;
	ob_start( 'tse_homepage_inject_filter' );
}

/**
 * Splices the carousel markup in before </body>. Returns the document
 * untouched if there's no closing body tag to anchor to.
 *
 * @param string $html
 * @return string
 */
function tse_homepage_inject_filter( $html ) {

	if ( stripos( $html, '</body>' ) === false ) return $html;
	if ( strpos( $html, 'tfb-home-js' ) !== false ) return $html;   // already patched

	ob_start();
	tse_inject_homepage_brands();
	$payload = ob_get_clean();

	if ( $payload === '' ) return $html;

	$pos = strripos( $html, '</body>' );
	return substr( $html, 0, $pos ) . $payload . substr( $html, $pos );
}

function tse_inject_homepage_brands() {

	if ( ! TFB_HOMEPAGE_PATCH ) return;

	$brands = tse_homepage_brand_order();
	if ( ! $brands ) return;

	$payload = wp_json_encode( [
		'brands'     => $brands,
		'logoBase'   => tse_brands_logo_base(),
		'brandsUrl'  => home_url( '/brands/' ),
		'catalogUrl' => TFB_CATALOG_URL,
		'perPage'    => 10,
	] );
	?>
<style id="tfb-home-css">
.tfb-band { padding: 64px 0; background: var(--cream, #F5F0E8); }
.tfb-band .wrap { max-width: var(--maxw, 1180px); margin: 0 auto; padding: 0 20px; }

.tfb-band .tfb-head { text-align: center; margin-bottom: 32px; }
.tfb-band .tfb-eyebrow {
  display: block;
  font-size: .75rem;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: var(--brass, #B8922A);
  margin-bottom: 8px;
}
.tfb-band h2 {
  margin: 0 0 10px;
  color: var(--ink, #1A1A18);
  text-wrap: balance;
}
.tfb-band .tfb-sub {
  margin: 0 auto;
  max-width: 60ch;
  color: var(--stone, #6B6B60);
}

/* Carousel shell: arrows flank a fixed-height track. */
.tfb-car { display: flex; align-items: center; gap: 12px; }

.tfb-arrow {
  flex: 0 0 auto;
  width: 42px; height: 42px;
  border: var(--border, 1px solid rgba(26,26,24,.12));
  border-radius: 50%;
  background: #fff;
  color: var(--forest, #2E4A35);
  font-size: 1.35rem;
  line-height: 1;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background .15s ease, border-color .15s ease;
}
.tfb-arrow:hover { background: var(--forest, #2E4A35); border-color: var(--forest, #2E4A35); color: #fff; }
.tfb-arrow:focus-visible { outline: 2px solid var(--brass, #B8922A); outline-offset: 2px; }

.tfb-track {
  flex: 1 1 auto;
  list-style: none;
  margin: 0; padding: 0;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
}

.tfb-cell {
  background: #fff;
  border: var(--border, 1px solid rgba(26,26,24,.12));
  border-radius: var(--radius, 6px);
  min-height: 92px;
  padding: 14px 12px;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 8px;
}
.tfb-cell img { height: 34px; width: auto; max-width: 100%; object-fit: contain; display: block; }
.tfb-cell span {
  font-size: .75rem; font-weight: 600;
  color: var(--stone, #6B6B60);
  text-align: center; line-height: 1.3;
}

.tfb-dots { display: flex; justify-content: center; gap: 7px; margin-top: 22px; }
.tfb-dot {
  width: 8px; height: 8px; padding: 0;
  border-radius: 50%; border: 0; cursor: pointer;
  background: rgba(26,26,24,.2);
  transition: background .15s ease;
}
.tfb-dot[aria-current="true"] { background: var(--forest, #2E4A35); }
.tfb-dot:focus-visible { outline: 2px solid var(--brass, #B8922A); outline-offset: 2px; }

.tfb-more { text-align: center; margin: 20px 0 0; }
.tfb-more a { color: var(--forest, #2E4A35); font-weight: 600; }

@media (max-width: 900px) { .tfb-track { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 560px) {
  .tfb-band { padding: 44px 0; }
  .tfb-track { grid-template-columns: repeat(2, 1fr); gap: 10px; }
  .tfb-arrow { width: 36px; height: 36px; }
  .tfb-cell { min-height: 78px; }
  .tfb-cell img { height: 28px; }
}
</style>

<script id="tfb-home-js">
(function () {
  "use strict";

  var CFG = <?php echo $payload; // phpcs:ignore — wp_json_encode output ?>;

  function build() {
    if (document.getElementById("tfb-band")) return;           // idempotent
    var hero = document.querySelector("section.hero");
    if (!hero) return;

    // ── 1. Carousel between the hero and #services ──
    var pages = [];
    for (var i = 0; i < CFG.brands.length; i += CFG.perPage) {
      pages.push(CFG.brands.slice(i, i + CFG.perPage));
    }

    var band = document.createElement("section");
    band.className = "tfb-band";
    band.id = "tfb-band";
    band.innerHTML =
      '<div class="wrap">' +
        '<div class="tfb-head">' +
          '<span class="tfb-eyebrow">Brands we supply</span>' +
          '<h2>Names you already trust.</h2>' +
          '<p class="tfb-sub">We decorate blanks from the same catalog the big shops buy from. ' +
            'Pick a brand you know, or tell us your budget and we&rsquo;ll match it.</p>' +
        '</div>' +
        '<div class="tfb-car">' +
          '<button type="button" class="tfb-arrow" data-dir="-1" aria-label="Show previous brands">&#8249;</button>' +
          '<ul class="tfb-track" id="tfb-track" aria-live="polite"></ul>' +
          '<button type="button" class="tfb-arrow" data-dir="1" aria-label="Show next brands">&#8250;</button>' +
        '</div>' +
        '<div class="tfb-dots" id="tfb-dots"></div>' +
        '<p class="tfb-more"><a href="' + CFG.brandsUrl + '">See every brand we carry &rarr;</a></p>' +
      '</div>';

    hero.parentNode.insertBefore(band, hero.nextSibling);

    var track = band.querySelector("#tfb-track");
    var dots  = band.querySelector("#tfb-dots");
    var page  = 0;

    function render() {
      track.innerHTML = pages[page].map(function (b) {
        return '<li class="tfb-cell">' +
                 '<img src="' + CFG.logoBase + b.file + '" alt="' + b.name + '" loading="lazy" decoding="async">' +
                 '<span>' + b.name + '</span>' +
               '</li>';
      }).join("");

      [].forEach.call(dots.children, function (d, i) {
        d.setAttribute("aria-current", i === page ? "true" : "false");
      });
    }

    pages.forEach(function (_, i) {
      var d = document.createElement("button");
      d.type = "button";
      d.className = "tfb-dot";
      d.setAttribute("aria-label", "Show brand set " + (i + 1) + " of " + pages.length);
      d.addEventListener("click", function () { page = i; render(); });
      dots.appendChild(d);
    });

    // Wraps at both ends so the arrows never dead-end.
    band.querySelectorAll(".tfb-arrow").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var dir = parseInt(btn.getAttribute("data-dir"), 10);
        page = (page + dir + pages.length) % pages.length;
        render();
      });
    });

    render();

    // ── 2. Industries section moves to the catalog page ──
    var industries = document.getElementById("industries");
    if (industries) industries.remove();

    // ── 3. Nav "Shop" becomes "Catalog" ──
    // The footer Explore list also links #industries, so scope this to the nav.
    var nav = document.querySelector("nav");
    if (nav) {
      nav.querySelectorAll('a[href="#industries"]').forEach(function (a) {
        a.textContent = "Catalog";
        a.setAttribute("href", CFG.catalogUrl);
      });
    }

    // ── 4. Footer Explore: retarget Shop, add Brands before How It Works ──
    var explore = null;
    document.querySelectorAll("footer h4").forEach(function (h) {
      if (h.textContent.trim().toLowerCase() === "explore") explore = h.parentNode;
    });

    if (explore) {
      var list = explore.querySelector("ul");
      if (list) {
        var shopLink = list.querySelector('a[href="#industries"]');
        if (shopLink) {
          shopLink.textContent = "Catalog";
          shopLink.setAttribute("href", CFG.catalogUrl);
        }

        var howItWorks = list.querySelector('a[href="#process"]');
        if (howItWorks && !list.querySelector('a[href="' + CFG.brandsUrl + '"]')) {
          var li = document.createElement("li");
          li.innerHTML = '<a href="' + CFG.brandsUrl + '">Brands</a>';
          list.insertBefore(li, howItWorks.parentNode);
        }
      }
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", build);
  } else {
    build();
  }
})();
</script>
	<?php
}
