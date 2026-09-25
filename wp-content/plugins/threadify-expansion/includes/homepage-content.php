<?php
/**
 * Threadify Expansion — Homepage content fixes (BETA, off by default)
 *
 *   1. Heat press is no longer offered. Every mention on the homepage is
 *      rewritten or removed — service card, quote form option, gallery filter,
 *      About chips, FAQ answer, hero copy, footer, the "four ways" headline and
 *      stat, and the meta description and structured data, so search results
 *      stop advertising it too.
 *   2. The quote form takes up to 10 design files instead of one.
 *
 * Both are HTML rewrites through the shared buffer in homepage-buffer.php.
 * They are independent of the carousel/catalog patch in homepage-brands.php
 * and have their own flag, so they can ship without it.
 *
 * Every rewrite states how many times its pattern must match. If the live
 * markup has drifted and a pattern no longer matches exactly that often, the
 * rewrite is skipped rather than guessed at: a stale pattern leaves a stray
 * "heat press" visible, which is harmless; a loose one could delete the wrong
 * block. Outcomes are recorded in $GLOBALS['tse_homepage_content_log'].
 *
 * To switch on: change TFB_HOMEPAGE_CONTENT below to true.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Master switch. */
if ( ! defined( 'TFB_HOMEPAGE_CONTENT' ) ) {
	define( 'TFB_HOMEPAGE_CONTENT', false );
}

if ( TFB_HOMEPAGE_CONTENT ) {
	add_filter( 'tse_homepage_html', 'tse_homepage_content_filter' );
}

/**
 * [ pattern, replacement, expected match count ] per spot.
 *
 * @return array<string,array{0:string,1:string,2:int}>
 */
function tse_heat_press_rewrites() {
	return [
		// The meta description and the JSON-LD description share one sentence.
		'seo description'  => [ '/DTF printing, custom patches, and heat press in Federal Way/',
		                        'DTF printing, and custom patches in Federal Way', 2 ],
		'hero copy'        => [ '/embroidery, DTF, custom patches, and heat press, all under one roof/',
		                        'embroidery, DTF, and custom patches, all under one roof', 1 ],
		'section headline' => [ '/Four ways to put your design on anything\./',
		                        'Three ways to put your design on anything.', 1 ],
		'hero stat'        => [ '#<div class="num">4</div>(\s*)<div class="lbl">Decoration methods</div>#',
		                        '<div class="num">3</div>$1<div class="lbl">Decoration methods</div>', 1 ],
		'service card'     => [ '#<div class="svc">\s*<div class="ic"[^>]*>[^<]*</div>\s*<h3>Heat Press</h3>.*?<div class="spec">[^<]*</div>\s*</div>\s*#s',
		                        '', 1 ],
		// Describes how patches attach, not the service — still reads as offering it.
		'patch card'       => [ '/iron, sew, or heat-press on/', 'iron or sew on', 1 ],
		'gallery filter'   => [ '#\s*<button class="filter-btn" data-filter="heatpress">Heat Press</button>#', '', 1 ],
		'about chip'       => [ '#\s*<span>Heat press</span>#', '', 1 ],
		'faq answer'       => [ '/; heat press for names and numbers\./', '.', 1 ],
		'quote option'     => [ '#\s*<option>Heat Press</option>#', '', 1 ],
		'footer blurb'     => [ '/Embroidery, DTF, custom patches, and heat press\./',
		                        'Embroidery, DTF, and custom patches.', 1 ],
		'footer methods'   => [ '#\s*<li><a href="\#services">Heat Press</a></li>#', '', 1 ],
	];
}

/**
 * Pieces of the multi-file upload. All-or-nothing: `multiple` only makes
 * sense once the submit hook exists, because without it several files would go
 * to Web3Forms under one field name and all but one would be lost.
 *
 * @return array<string,array{0:string,1:string,2:int}>
 */
function tse_upload_rewrites() {
	return [
		'file input'  => [ '#<input type="file" id="artwork" name="artwork" accept=#',
		                   '<input type="file" id="artwork" name="artwork" multiple accept=', 1 ],
		'file label'  => [ '#<label for="artwork">Attach your logo or design</label>#',
		                   '<label for="artwork">Attach your design files</label>', 1 ],
		'file hint'   => [ '#<div class="file-hint">[^<]*</div>#',
		                   '<div class="file-hint">Up to 10 files, 5 MB each.</div>'
		                   . '<ul class="tse-file-list" id="tseFileList" hidden></ul>'
		                   . '<p class="tse-file-err" id="tseFileErr" role="alert" hidden></p>', 1 ],
		'submit hook' => [ '#var fd = new FormData\(quoteForm\);#',
		                   'var fd = new FormData(quoteForm); if (window.tseSplitArtwork) window.tseSplitArtwork(fd);', 1 ],
	];
}

/**
 * @param string $html
 * @param array  $rules
 * @param bool   $all_or_nothing
 * @return string
 */
function tse_apply_rewrites( $html, $rules, $all_or_nothing = false ) {

	$counts = [];
	foreach ( $rules as $name => $r ) {
		$counts[ $name ] = preg_match_all( $r[0], $html );
	}

	if ( $all_or_nothing ) {
		foreach ( $rules as $name => $r ) {
			if ( $counts[ $name ] !== $r[2] ) {
				foreach ( $rules as $n => $_ ) {
					$GLOBALS['tse_homepage_content_log'][ $n ] =
						"skipped: group aborted because '$name' matched {$counts[ $name ]}, expected {$r[2]}";
				}
				return $html;
			}
		}
	}

	foreach ( $rules as $name => $r ) {
		if ( $counts[ $name ] !== $r[2] ) {
			$GLOBALS['tse_homepage_content_log'][ $name ] = "skipped: matched {$counts[ $name ]}, expected {$r[2]}";
			continue;
		}
		$html = preg_replace( $r[0], $r[1], $html );
		$GLOBALS['tse_homepage_content_log'][ $name ] = 'ok';
	}

	return $html;
}

/**
 * @param string $html
 * @return string
 */
function tse_homepage_content_filter( $html ) {

	if ( strpos( $html, 'id="tse-content-js"' ) !== false ) return $html;   // already patched

	$GLOBALS['tse_homepage_content_log'] = [];

	$html = tse_apply_rewrites( $html, tse_heat_press_rewrites() );
	$html = tse_apply_rewrites( $html, tse_upload_rewrites(), true );

	$log     = $GLOBALS['tse_homepage_content_log'];
	$payload = '';

	// Three service cards now; the grid was built for four.
	if ( ( $log['service card'] ?? '' ) === 'ok' ) {
		$payload .= '<style id="tse-content-svc">
#tdfy .svc-grid{ grid-template-columns: repeat(3, 1fr); }
@media (max-width: 900px){ #tdfy .svc-grid{ grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 700px){ #tdfy .svc-grid{ grid-template-columns: 1fr; } }
</style>';
	}

	if ( ( $log['submit hook'] ?? '' ) === 'ok' ) {
		ob_start();
		tse_upload_assets();
		$payload .= ob_get_clean();
	}

	return tse_homepage_before_body_end( $html, $payload );
}

function tse_upload_assets() {
	?>
<style id="tse-content-upload">
#tdfy .tse-file-list{ list-style: none; margin: 10px 0 0; padding: 0; display: grid; gap: 6px; }
#tdfy .tse-file-list[hidden]{ display: none; }
#tdfy .tse-file-list li{
  display: flex; justify-content: space-between; gap: 12px;
  padding: 7px 10px; font-size: .8125rem;
  background: var(--cream, #F5F0E8); border: var(--border, 1px solid rgba(26,26,24,.12));
  border-radius: var(--radius, 6px);
}
#tdfy .tse-file-list li span:first-child{ overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
#tdfy .tse-file-list li span:last-child{ flex: none; color: var(--stone, #6B6B60); font-variant-numeric: tabular-nums; }
#tdfy .tse-file-err{ margin: 8px 0 0; font-size: .8125rem; font-weight: 600; color: #9E2B25; }
</style>
<script id="tse-content-js">
(function () {
  "use strict";

  // Web3Forms' documented limit is 5 MB per attachment. It publishes no total,
  // so 25 MB is held as the ceiling: most mail providers, Gmail included,
  // refuse messages larger than that.
  var MAX_FILES = 10;
  var MAX_EACH  = 5 * 1024 * 1024;
  var MAX_TOTAL = 25 * 1024 * 1024;
  var ALLOWED   = /\.(png|jpe?g|svg|pdf|ai|eps|psd)$/i;

  var input = document.getElementById("artwork");
  var list  = document.getElementById("tseFileList");
  var err   = document.getElementById("tseFileErr");
  if (!input || !list || !err) return;

  function size(n) {
    return n < 1048576 ? Math.max(1, Math.round(n / 1024)) + " KB" : (n / 1048576).toFixed(1) + " MB";
  }
  function esc(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }
  function clearList() { list.innerHTML = ""; list.hidden = true; }
  function reject(msg) {
    input.value = "";
    clearList();
    err.textContent = msg;
    err.hidden = false;
  }

  input.addEventListener("change", function () {
    err.hidden = true;
    var files = Array.prototype.slice.call(input.files || []);
    if (!files.length) { clearList(); return; }

    if (files.length > MAX_FILES) {
      return reject("You picked " + files.length + " files. Please choose " + MAX_FILES + " or fewer.");
    }

    var total = 0;
    for (var i = 0; i < files.length; i++) {
      var f = files[i];
      if (!ALLOWED.test(f.name)) {
        return reject(f.name + " isn't a supported type. Use PNG, JPG, SVG, PDF, AI, EPS or PSD.");
      }
      if (f.size > MAX_EACH) {
        return reject(f.name + " is " + size(f.size) + ". Each file needs to be 5 MB or less.");
      }
      total += f.size;
    }
    if (total > MAX_TOTAL) {
      return reject("Those files add up to " + size(total) + ". Please keep the total under 25 MB.");
    }

    list.innerHTML = files.map(function (f) {
      return "<li><span>" + esc(f.name) + "</span><span>" + size(f.size) + "</span></li>";
    }).join("");
    list.hidden = false;
  });

  if (input.form) input.form.addEventListener("reset", function () { clearList(); err.hidden = true; });

  // Web3Forms only delivers several attachments when each has its own field
  // name, so the picker's files are re-sent as artwork_1 … artwork_10.
  window.tseSplitArtwork = function (fd) {
    var files = fd.getAll("artwork").filter(function (f) { return f && f.size; });
    fd.delete("artwork");
    files.slice(0, MAX_FILES).forEach(function (f, i) {
      fd.append("artwork_" + (i + 1), f, f.name);
    });
  };
})();
</script>
	<?php
}
