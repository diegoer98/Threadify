<?php
/**
 * Threadify Expansion — /events/ rewrite + event inquiry form (BETA, off)
 *
 * /events/ is database markup echoed by snippet id=6, like the homepage, so
 * this replaces its body sections through the shared buffer in
 * homepage-buffer.php. The nav, footer, head and inline styles are kept; only
 * the content between the hero and the footer is swapped, and every "Book us"
 * link now points at the page's own inquiry form instead of the homepage's
 * general quote form.
 *
 * Copy rules: no prices and no promises of specific numbers (turnaround,
 * capacity, crowd sizes). Photos are the shop's own booth shots already used on
 * the page — no stock imagery.
 *
 * The form posts to Web3Forms with the same access key as the homepage quote
 * form, so inquiries arrive at the same inbox, with an "event inquiry" subject
 * so they sort apart from general quotes. If the request fails it falls back to
 * a pre-filled mailto link, as the homepage form does.
 *
 * The rewrite is all-or-nothing: if the live page no longer has exactly one
 * hero and one footer to anchor to, it is left untouched.
 *
 * To switch on: change TFB_EVENTS_PAGE below to true.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Master switch. */
if ( ! defined( 'TFB_EVENTS_PAGE' ) ) {
	define( 'TFB_EVENTS_PAGE', false );
}

/** Same key the homepage quote form uses; it is registered to the orders inbox. */
define( 'TFB_EVENTS_WEB3FORMS_KEY', '065b1dbe-6af3-44ad-9830-2e64b10234c4' );
define( 'TFB_EVENTS_ORDERS_EMAIL', 'orders@threadifyapparel.com' );

if ( TFB_EVENTS_PAGE ) {
	add_filter( 'tse_events_html', 'tse_events_page_filter' );
}

/**
 * @param string $html
 * @return string
 */
function tse_events_page_filter( $html ) {

	if ( strpos( $html, 'id="tev-inquiry-js"' ) !== false ) return $html;   // already patched

	$GLOBALS['tse_events_log'] = [];

	$start = '<section class="hero">';
	$end   = '<footer class="site">';
	$nav   = '#<a([^>]*)href="/\#contact"([^>]*)>Book Us</a>#';

	$counts = [
		'hero'    => substr_count( $html, $start ),
		'footer'  => substr_count( $html, $end ),
		'nav cta' => preg_match_all( $nav, $html ),
	];
	foreach ( $counts as $name => $n ) {
		if ( $n !== 1 ) {
			$GLOBALS['tse_events_log']['aborted'] = "'$name' matched $n, expected 1";
			return $html;
		}
	}

	$a = strpos( $html, $start );
	$b = strpos( $html, $end );
	if ( $b < $a ) {
		$GLOBALS['tse_events_log']['aborted'] = 'footer precedes hero';
		return $html;
	}

	$html = substr( $html, 0, $a ) . tse_events_body() . "\n\n  " . substr( $html, $b );
	$html = preg_replace( $nav, '<a$1href="#inquiry"$2>Book Us</a>', $html );

	ob_start();
	tse_events_assets();
	$html = tse_homepage_before_body_end( $html, ob_get_clean() );

	$GLOBALS['tse_events_log']['status'] = 'ok';
	return $html;
}

/** The replacement sections, hero through inquiry form. */
function tse_events_body() {
	$img = 'https://threadifyapparel.com/wp-content/uploads/2026/07/';
	ob_start();
	?>
<section class="hero">
    <div class="wrap hero-grid">
      <div>
        <span class="eyebrow">Events</span>
        <h1>Give your crowd<br /><span class="accent">something to take&nbsp;home.</span></h1>
        <p class="lede">
          Book Threadify as a vendor and we'll bring the booth, premade merch ready to sell, and
          <strong>live embroidery</strong> your guests can watch happen — names, numbers and logos
          stitched right there. It's the booth people gather around.
        </p>
        <div class="hero-cta">
          <a href="#inquiry" class="btn btn-primary">Check my date</a>
          <a href="#how" class="btn btn-ghost">See how it works</a>
        </div>
      </div>
      <div class="hero-media">
        <img src="<?php echo $img; ?>threadify-pics8.jpg" alt="The Threadify crew running their vendor booth at an event" />
        <span class="hero-badge">Live on-site embroidery</span>
      </div>
    </div>
  </section>

  <section class="alt">
    <div class="wrap two">
      <img src="<?php echo $img; ?>threadify-pics6.jpg" alt="Threadify booth with embroidered totes ready to sell at an event" />
      <div>
        <span class="eyebrow">You host, we handle the booth</span>
        <h2>A crowd-pleaser that's off your to-do list.</h2>
        <p>
          We arrive with the gear, the crew and the stock, set up a clean branded booth that fits your
          event, and run it start to finish. You get a standout attraction without adding a single thing
          to manage.
        </p>
        <ul class="ticks">
          <li>Live embroidery guests stop to watch</li>
          <li>Premade merch on the table from the moment doors open</li>
          <li>Names, numbers and logos personalized on the spot</li>
          <li>Our gear, our crew, our setup — nothing extra for you</li>
        </ul>
      </div>
    </div>
  </section>

  <section id="how">
    <div class="wrap">
      <div class="section-head">
        <span class="eyebrow">How it works</span>
        <h2>Booking us is simple.</h2>
      </div>
      <div class="cards">
        <div class="card"><div class="num">01</div><h3>Tell us about your event</h3><p>Share the date, the place and the kind of crowd you're expecting using the form below.</p></div>
        <div class="card"><div class="num">02</div><h3>We plan the booth</h3><p>We confirm your date and shape the setup around your event — live embroidery, premade merch, or both.</p></div>
        <div class="card"><div class="num">03</div><h3>Doors open, we go live</h3><p>We set up, sell and stitch on the spot while you enjoy your event.</p></div>
      </div>
    </div>
  </section>

  <section class="alt">
    <div class="wrap">
      <div class="section-head tev-center">
        <span class="eyebrow">Great for</span>
        <h2>Made for any crowd.</h2>
      </div>
      <ul class="tev-fit">
        <li>Markets &amp; pop-ups</li><li>Festivals &amp; fairs</li><li>Game days &amp; tournaments</li>
        <li>School &amp; spirit nights</li><li>Company events</li><li>Grand openings</li><li>Conventions</li>
      </ul>
    </div>
  </section>

  <section id="inquiry">
    <div class="wrap tev-wrap">
      <div class="section-head tev-head">
        <span class="eyebrow">Book an event</span>
        <h2>Let's bring the booth to your event.</h2>
        <p>Tell us a little about it and we'll confirm availability by email. The earlier you reach out, the easier it is to hold your date.</p>
      </div>

      <form class="tev-form" id="eventForm" novalidate>
        <div class="tev-row">
          <div class="tev-field">
            <label for="ev-name">Name <span class="req">*</span></label>
            <input type="text" id="ev-name" name="name" autocomplete="name" required />
          </div>
          <div class="tev-field">
            <label for="ev-email">Email <span class="req">*</span></label>
            <input type="email" id="ev-email" name="email" autocomplete="email" required />
          </div>
        </div>

        <div class="tev-row">
          <div class="tev-field">
            <label for="ev-phone">Phone</label>
            <input type="tel" id="ev-phone" name="phone" autocomplete="tel" />
          </div>
          <div class="tev-field">
            <label for="ev-org">Organization or group</label>
            <input type="text" id="ev-org" name="organization" autocomplete="organization" />
          </div>
        </div>

        <div class="tev-row">
          <div class="tev-field">
            <label for="ev-type">Type of event <span class="req">*</span></label>
            <select id="ev-type" name="event_type" required>
              <option value="" selected disabled>Choose one…</option>
              <option>Market or pop-up</option>
              <option>Festival or fair</option>
              <option>Game day or tournament</option>
              <option>School or spirit night</option>
              <option>Company event</option>
              <option>Grand opening</option>
              <option>Convention</option>
              <option>Something else</option>
            </select>
          </div>
          <div class="tev-field">
            <label for="ev-date">Event date <span class="req">*</span></label>
            <input type="date" id="ev-date" name="event_date" required />
          </div>
        </div>

        <div class="tev-row">
          <div class="tev-field">
            <label for="ev-location">Location <span class="req">*</span></label>
            <input type="text" id="ev-location" name="event_location" placeholder="City or venue" required />
          </div>
          <div class="tev-field">
            <label for="ev-crowd">Expected crowd</label>
            <select id="ev-crowd" name="expected_crowd">
              <option value="" selected>Not sure yet</option>
              <option>Under 100</option>
              <option>100 – 500</option>
              <option>500 – 1,000</option>
              <option>1,000+</option>
            </select>
          </div>
        </div>

        <fieldset class="tev-field tev-choice">
          <legend>What sounds good?</legend>
          <label><input type="radio" name="interested_in" value="Live embroidery" /> Live embroidery</label>
          <label><input type="radio" name="interested_in" value="Premade merch" /> Premade merch</label>
          <label><input type="radio" name="interested_in" value="Both" checked /> Both</label>
        </fieldset>

        <div class="tev-field">
          <label for="ev-details">Anything else we should know?</label>
          <textarea id="ev-details" name="message" rows="4" placeholder="Hours, setup space, your logo, the vibe you're going for…"></textarea>
        </div>

        <input type="checkbox" name="botcheck" class="tev-hp" tabindex="-1" autocomplete="off" aria-hidden="true" />
        <button type="submit" class="btn btn-primary" id="eventSubmitBtn">Check my date</button>
        <div class="tev-msg" id="eventMsg" role="status" aria-live="polite" hidden></div>
      </form>
    </div>
  </section>
	<?php
	return ob_get_clean();
}

function tse_events_assets() {
	?>
<style id="tev-inquiry-css">
/* Evens out the longer headlines so a last word doesn't sit alone on a line. */
#tdfy .hero h1, #tdfy .tev-head h2{ text-wrap: balance; }
#tdfy .tev-wrap{ max-width: 760px; }

/* .section-head and its children carry max-widths, so centring needs auto
   margins on the block itself as well as inside it. */
#tdfy .section-head.tev-center, #tdfy .section-head.tev-head{ margin-left: auto; margin-right: auto; }
#tdfy .tev-center{ text-align: center; }
#tdfy .tev-center > *{ margin-left: auto; margin-right: auto; }

/* Equal tiles in centred rows: seven events sit 4 over 3 on desktop and
   2-2-2-1 on phones, mirrored around the centre line either way. */
#tdfy .tev-fit{
  --gap: 12px;
  list-style: none; margin: 8px auto 0; padding: 0; max-width: 920px;
  display: flex; flex-wrap: wrap; justify-content: center; gap: var(--gap);
}
#tdfy .tev-fit li{
  flex: 0 0 calc((100% - 3 * var(--gap)) / 4);
  box-sizing: border-box;
  display: flex; align-items: center; justify-content: center; text-align: center;
  min-height: 64px; padding: 12px 14px;
  font-size: .9375rem; font-weight: 600; color: var(--forest);
  background: #fff; border: var(--border); border-radius: var(--radius);
}
@media (max-width: 640px){
  /* Tall enough for a two-line name, so every tile matches. */
  #tdfy .tev-fit li{ flex-basis: calc((100% - var(--gap)) / 2); min-height: 72px; font-size: .875rem; }
}
#tdfy .tev-head{ text-align: center; }
#tdfy .tev-head p{ margin-left: auto; margin-right: auto; }
#tdfy .tev-form{ background: var(--cream); border: var(--border); border-radius: var(--radius); padding: 30px; }
#tdfy .tev-row{ display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
#tdfy .tev-field{ margin: 0 0 18px; }
#tdfy .tev-field > label, #tdfy .tev-choice legend{
  display: block; margin-bottom: 6px; font-size: .875rem; font-weight: 600; color: var(--ink);
}
#tdfy .tev-field .req{ color: var(--brass); }
#tdfy .tev-form input[type="text"], #tdfy .tev-form input[type="email"], #tdfy .tev-form input[type="tel"],
#tdfy .tev-form input[type="date"], #tdfy .tev-form select, #tdfy .tev-form textarea{
  width: 100%; box-sizing: border-box; padding: 11px 12px; font: inherit; font-size: .95rem; color: var(--ink);
  background: #fff; border: var(--border); border-radius: var(--radius);
}
#tdfy .tev-form textarea{ resize: vertical; min-height: 110px; }
#tdfy .tev-form input:focus, #tdfy .tev-form select:focus, #tdfy .tev-form textarea:focus{
  outline: 2px solid var(--forest); outline-offset: 1px; border-color: var(--forest);
}
#tdfy .tev-form [aria-invalid="true"]{ border-color: #9E2B25; }
#tdfy .tev-choice{ border: 0; padding: 0; min-width: 0; }
#tdfy .tev-choice label{
  display: inline-flex; align-items: center; gap: 8px; margin: 0 8px 8px 0; padding: 9px 14px;
  font-size: .875rem; font-weight: 600; background: #fff; border: var(--border); border-radius: 100px; cursor: pointer;
}
#tdfy .tev-choice input{ accent-color: var(--forest); margin: 0; }
#tdfy .tev-choice label:has(input:checked){ border-color: var(--forest); }
#tdfy .tev-choice label:has(input:focus-visible){ outline: 2px solid var(--brass); outline-offset: 2px; }
#tdfy .tev-hp{ position: absolute; left: -9999px; }
#tdfy .tev-form .btn-primary{ width: 100%; justify-content: center; margin-top: 6px; }
#tdfy .tev-form .btn-primary[disabled]{ opacity: .7; cursor: default; }
#tdfy .tev-msg{ margin-top: 14px; padding: 14px 16px; border-radius: var(--radius); font-size: .9rem;
  background: var(--parchment); border: 1px solid var(--forest); }
#tdfy .tev-msg[hidden]{ display: none; }
#tdfy .tev-msg.is-error{ border-color: var(--brass); }
#tdfy .tev-msg a{ color: var(--forest); font-weight: 600; }
@media (max-width: 640px){
  #tdfy .tev-row{ grid-template-columns: 1fr; gap: 0; }
  #tdfy .tev-form{ padding: 22px 18px; }
}
</style>
<script id="tev-inquiry-js">
(function () {
  "use strict";

  var KEY    = <?php echo json_encode( TFB_EVENTS_WEB3FORMS_KEY ); ?>;
  var ORDERS = <?php echo json_encode( TFB_EVENTS_ORDERS_EMAIL ); ?>;

  var form = document.getElementById("eventForm");
  if (!form) return;
  var btn  = document.getElementById("eventSubmitBtn");
  var msg  = document.getElementById("eventMsg");
  var date = document.getElementById("ev-date");

  // No booking a date that has already passed.
  var t = new Date();
  date.min = t.getFullYear() + "-" + String(t.getMonth() + 1).padStart(2, "0") + "-" + String(t.getDate()).padStart(2, "0");

  function val(id) { return document.getElementById(id).value.trim(); }
  function esc(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }
  function niceDate(v) {
    if (!v) return "";
    var p = v.split("-");
    return new Date(+p[0], +p[1] - 1, +p[2]).toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });
  }
  function show(html, isError) {
    msg.innerHTML = html;
    msg.classList.toggle("is-error", !!isError);
    msg.hidden = false;
  }
  function mailto() {
    var body = "Name: " + val("ev-name") + "\nEmail: " + val("ev-email") + "\nPhone: " + (val("ev-phone") || "—") +
      "\nOrganization: " + (val("ev-org") || "—") + "\nEvent type: " + val("ev-type") +
      "\nDate: " + niceDate(val("ev-date")) + "\nLocation: " + val("ev-location") +
      "\nExpected crowd: " + (val("ev-crowd") || "Not sure yet") + "\n\n" + val("ev-details");
    return "mailto:" + ORDERS + "?subject=" + encodeURIComponent("Event inquiry — " + val("ev-type")) +
      "&body=" + encodeURIComponent(body);
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    var first = null;
    form.querySelectorAll("[required]").forEach(function (el) {
      var v = el.value.trim();
      var bad = !v
        || (el.type === "email" && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v))
        || (el.type === "date" && el.min && v < el.min);
      el.setAttribute("aria-invalid", bad ? "true" : "false");
      if (bad && !first) first = el;
    });
    if (first) {
      show(first.type === "date" && first.value
        ? "That date has already passed. Please pick an upcoming one."
        : "Please fill in the highlighted fields so we can check your date.", true);
      first.focus();
      return;
    }

    var fd = new FormData(form);
    var firstName = val("ev-name").split(" ")[0];
    var type = val("ev-type");
    var when = niceDate(val("ev-date"));
    fd.set("event_date", when);
    fd.append("access_key", KEY);
    fd.append("subject", "New event inquiry — " + type + " on " + when);
    fd.append("from_name", "Threadify Events Page");
    fd.append("replyto", val("ev-email"));

    btn.disabled = true;
    btn.textContent = "Sending…";

    fetch("https://api.web3forms.com/submit", { method: "POST", body: fd })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d || !d.success) throw new Error("not accepted");
        form.reset();
        form.querySelectorAll("[aria-invalid]").forEach(function (el) { el.removeAttribute("aria-invalid"); });
        show("Thanks, " + esc(firstName) + "! Your event inquiry is in. We'll confirm availability from " + ORDERS + ".");
        btn.textContent = "Inquiry sent";
      })
      .catch(function () {
        show("That didn't go through. You can <a href=\"" + mailto() + "\">email us your event details</a> instead, and we'll pick it up from there.", true);
        btn.disabled = false;
        btn.textContent = "Check my date";
      });
  });
})();
</script>
	<?php
}
