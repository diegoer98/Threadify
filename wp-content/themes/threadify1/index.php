<?php
/**
 * Threadify – index.php
 * Single-template theme: renders the full one-page site.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ══ NAV ══════════════════════════════════════════════ -->
<nav id="site-nav" role="navigation">
  <div class="wrap">
    <a class="nav-logo" href="#welcome">Threadify</a>

    <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <div class="nav-links" id="nav-links">
      <a href="#welcome">Welcome</a>
      <a href="#services">Services</a>
      <a href="#portfolio">Our Work</a>
      <a href="#testimonials">Reviews</a>
      <a href="#contact">Contact</a>
      <a href="https://shop.companycasuals.com/" target="_blank" rel="noopener" class="nav-cta">Catalog</a>
    </div>
  </div>
</nav>

<!-- ══ HERO ══════════════════════════════════════════════ -->
<section id="welcome">
  <div class="hero-bg"></div>
  <div class="hero-grid"></div>
  <div class="wrap">
    <div class="hero-inner">
      <div class="hero-eyebrow">Seattle, Washington</div>
      <h1 class="hero-title">Threadify<br><em>Custom Embroidery</em></h1>
      <p class="hero-desc">
        Custom embroidery for businesses, teams, and creators—made to look sharp, feel premium, and last.
        From digitizing to final stitch, we keep it simple, fast, and high-quality.
      </p>
      <div class="hero-actions">
        <a href="#contact" class="btn btn-primary">Get a Quote</a>
        <a href="https://shop.companycasuals.com/" target="_blank" rel="noopener" class="btn btn-secondary">Browse Catalog</a>
      </div>
      <div class="hero-badges">
        <span>Local service</span>
        <span>Fast turnaround</span>
        <span>Consistent, professional results</span>
      </div>
    </div>
  </div>
</section>

<!-- ══ SERVICES ══════════════════════════════════════════ -->
<section id="services" class="section">
  <div class="wrap">
    <div class="section-label">What We Do</div>
    <h2 class="section-title">Our Services</h2>
    <p class="section-sub">A streamlined process for clean, consistent embroidery—whether you need one piece or a full team order.</p>

    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon">🧵</div>
        <h3>Custom Embroidery</h3>
        <p>Premium embroidery on garments, hats, bags, and more. Our commercial-grade equipment and careful QC deliver crisp detail, clean edges, and consistent results from first stitch to last.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">✏️</div>
        <h3>Design &amp; Digitizing</h3>
        <p>We convert logos and artwork into stitch files that sew beautifully. Expect thoughtful stitch direction, density, and sizing so your design looks sharp on real garments.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">📦</div>
        <h3>Bulk Orders</h3>
        <p>Outfitting a team or company? We handle bulk orders with reliable timelines, clear communication, and consistent placement across every item.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">⚡</div>
        <h3>Rush Service</h3>
        <p>On a deadline? We offer expedited production when capacity allows. Send your details and we'll confirm timing before you commit.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">✅</div>
        <h3>Quality Guarantee</h3>
        <p>Every piece is inspected before it ships. If something isn't right, we'll make it right—simple as that.</p>
      </div>
      <div class="service-card">
        <div class="service-icon">💬</div>
        <h3>Order Support</h3>
        <p>Not sure what blank to pick? We'll help you choose the right garment and decoration approach based on your budget and use case.</p>
      </div>
    </div>

    <div class="order-steps">
      <h3>How to Order</h3>
      <div class="steps-list">
        <div class="step">
          <div class="step-num">1</div>
          <div class="step-text"><strong>Browse the Catalog</strong><br>Note the product code, color, and sizes you want.</div>
        </div>
        <div class="step">
          <div class="step-num">2</div>
          <div class="step-text"><strong>Send Details</strong><br>Share the item details and your logo or artwork.</div>
        </div>
        <div class="step">
          <div class="step-num">3</div>
          <div class="step-text"><strong>Get a Quote</strong><br>We'll reply with a clear quote and timeline including decoration.</div>
        </div>
        <div class="step">
          <div class="step-num">4</div>
          <div class="step-text"><strong>We Handle the Rest</strong><br>Once approved and paid, we order blanks, embroider, and deliver.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ PORTFOLIO ══════════════════════════════════════════ -->
<section id="portfolio" class="section">
  <div class="wrap">
    <div class="section-label">Examples</div>
    <h2 class="section-title">Our Work</h2>
    <p class="section-sub">Examples of what we love making—clean branding, confident stitching, and details that hold up.</p>

    <div class="portfolio-grid">
      <div class="portfolio-item"><span class="pi-icon">👕</span><span class="pi-title">Team &amp; Club Apparel</span></div>
      <div class="portfolio-item"><span class="pi-icon">💼</span><span class="pi-title">Corporate Branding</span></div>
      <div class="portfolio-item"><span class="pi-icon">🧢</span><span class="pi-title">Hats &amp; Headwear</span></div>
      <div class="portfolio-item"><span class="pi-icon">🧥</span><span class="pi-title">Jackets &amp; Outerwear</span></div>
      <div class="portfolio-item"><span class="pi-icon">🎪</span><span class="pi-title">Event Merchandise</span></div>
      <div class="portfolio-item"><span class="pi-icon">🎁</span><span class="pi-title">Personalized Gifts</span></div>
    </div>

    <div class="portfolio-note">
      <strong>💡 Every order is customized.</strong> Send your logo and we'll recommend sizing and placement that looks best.
    </div>
  </div>
</section>

<!-- ══ TESTIMONIALS ════════════════════════════════════════ -->
<section id="testimonials" class="section">
  <div class="wrap">
    <div class="section-label">Reviews</div>
    <h2 class="section-title">What Customers Say</h2>
    <p class="section-sub">A few words from people who needed quality, speed, and a smooth process.</p>

    <div class="testimonials-grid">
      <div class="testimonial">
        <div class="stars">★★★★★</div>
        <blockquote>"Dropped off a couple shirts for embroidery and they came out perfect. Super timely and easy to work with."</blockquote>
        <cite>Trenton Nield</cite>
      </div>
      <div class="testimonial">
        <div class="stars">★★★★★</div>
        <blockquote>"Great quality embroidery on our restaurant aprons. Clean stitching that holds up well after washing. Would definitely order again."</blockquote>
        <cite>Red Wagon Burger</cite>
      </div>
      <div class="testimonial">
        <div class="stars">★★★★★</div>
        <blockquote>"Brought in my own jackets for embroidery and couldn't be happier with the results. The stitching looks great and feels very high quality."</blockquote>
        <cite>Fonzie Gabon</cite>
      </div>
    </div>
  </div>
</section>

<!-- ══ CONTACT ════════════════════════════════════════════ -->
<section id="contact" class="section">
  <div class="wrap">
    <div class="section-label">Reach Out</div>
    <h2 class="section-title">Get In Touch</h2>
    <p class="section-sub">Send your product details and artwork—we'll reply with a quote and next steps.</p>

    <div class="contact-grid">
      <!-- Left: contact details -->
      <div class="contact-info">
        <div class="contact-detail">
          <div class="cd-icon">📧</div>
          <div>
            <div class="cd-label">Email</div>
            <div class="cd-val"><a href="mailto:Orders@ThreadifyApparel.com">Orders@ThreadifyApparel.com</a></div>
          </div>
        </div>
        <div class="contact-detail">
          <div class="cd-icon">📱</div>
          <div>
            <div class="cd-label">Phone</div>
            <div class="cd-val"><a href="tel:+12532491545">(253) 249-1545</a></div>
          </div>
        </div>
        <div class="contact-detail">
          <div class="cd-icon">📍</div>
          <div>
            <div class="cd-label">Location</div>
            <div class="cd-val">Seattle, Washington</div>
          </div>
        </div>
      </div>

      <!-- Right: form -->
      <div class="contact-form-wrap">
        <h3>Quick Contact Form</h3>

        <form id="threadify-contact-form" novalidate>
          <?php wp_nonce_field( 'threadify_contact_nonce', 'threadify_nonce' ); ?>

          <div class="form-group">
            <label for="tf-name">Name <span style="color:#ff8080">*</span></label>
            <input type="text" id="tf-name" name="name" placeholder="Your name" required>
          </div>

          <div class="form-group">
            <label for="tf-email">Email <span style="color:#ff8080">*</span></label>
            <input type="email" id="tf-email" name="email" placeholder="your@email.com" required>
          </div>

          <div class="form-group">
            <label for="tf-message">Project Details <span style="color:#ff8080">*</span></label>
            <textarea id="tf-message" name="message" placeholder="Tell us about your project, quantity, garments, timeline…" required></textarea>
          </div>

          <!-- File Upload -->
          <div class="form-group">
            <label>Attach a File <span style="opacity:.5;font-weight:400">(optional – logo, artwork, reference)</span></label>
            <div class="upload-drop-zone" id="tf-drop-zone">
              <input type="file" id="tf-file" name="threadify_attachment"
                     accept=".jpg,.jpeg,.png,.gif,.pdf,.ai,.eps,.svg,.zip">
              <span class="upload-icon">📎</span>
              <span class="upload-label-text"><span>Click to browse</span> or drag &amp; drop</span>
              <span class="upload-hint">JPG, PNG, PDF, AI, EPS, SVG, ZIP — max 10 MB</span>
            </div>
            <div class="upload-preview" id="tf-preview">
              <span class="file-name" id="tf-file-name"></span>
              <button type="button" class="remove-file" id="tf-remove" aria-label="Remove file">✕</button>
            </div>
            <span class="upload-error" id="tf-upload-error"></span>
          </div>

          <button type="submit" class="form-submit-btn" id="tf-submit">Send Message</button>
          <p class="form-note">We typically respond within 24 hours.</p>
          <div class="form-success" id="tf-success">
            ✓ Message sent! We'll be in touch within 24 hours.
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- ══ FOOTER ══════════════════════════════════════════════ -->
<footer id="site-footer">
  <div class="wrap">
    <span>© <?php echo date('Y'); ?> Threadify. All rights reserved. | Custom Embroidery | Seattle, WA</span>
    <span style="color:var(--accent2)">Orders@ThreadifyApparel.com</span>
  </div>
</footer>

<?php wp_footer(); ?>

<script>
/* ── Mobile nav toggle ── */
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('nav-toggle');
  var links  = document.getElementById('nav-links');
  if (!toggle || !links) return;

  toggle.addEventListener('click', function () {
    var open = links.classList.contains('open');
    links.classList.toggle('open', !open);
    toggle.setAttribute('aria-expanded', String(!open));
  });

  links.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () {
      links.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    });
  });
});

/* ── Contact form (uses jQuery enqueued by WordPress) ── */
document.addEventListener('DOMContentLoaded', function () {
  /* upload field */
  var zone    = document.getElementById('tf-drop-zone');
  var fileIn  = document.getElementById('tf-file');
  var preview = document.getElementById('tf-preview');
  var fname   = document.getElementById('tf-file-name');
  var errEl   = document.getElementById('tf-upload-error');
  var removeBtn = document.getElementById('tf-remove');
  var MAX     = 10 * 1024 * 1024;
  var ALLOWED = /\.(jpe?g|png|gif|pdf|ai|eps|svg|zip)$/i;

  function resetUpload() {
    fileIn.value = '';
    preview.classList.remove('visible');
    zone.querySelector('.upload-label-text').innerHTML = '<span>Click to browse</span> or drag &amp; drop';
    errEl.textContent = '';
    errEl.classList.remove('visible');
  }

  function showFile(file) {
    errEl.textContent = '';
    errEl.classList.remove('visible');
    if (!file) return;
    if (!ALLOWED.test(file.name)) {
      errEl.textContent = 'File type not allowed. Use JPG, PNG, PDF, AI, EPS, SVG, or ZIP.';
      errEl.classList.add('visible');
      fileIn.value = ''; return;
    }
    if (file.size > MAX) {
      errEl.textContent = 'File is too large (max 10 MB).';
      errEl.classList.add('visible');
      fileIn.value = ''; return;
    }
    fname.textContent = file.name;
    preview.classList.add('visible');
    zone.querySelector('.upload-label-text').innerHTML = '<span>File selected</span>';
  }

  if (zone) {
    zone.addEventListener('dragover',  function (e) { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragenter', function (e) { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', function ()  { zone.classList.remove('dragover'); });
    zone.addEventListener('drop',      function ()  { zone.classList.remove('dragover'); });
  }
  if (fileIn)    fileIn.addEventListener('change', function () { showFile(this.files[0]); });
  if (removeBtn) removeBtn.addEventListener('click', resetUpload);

  /* contact form submit */
  var form = document.getElementById('threadify-contact-form');
  var btn  = document.getElementById('tf-submit');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var name    = document.getElementById('tf-name').value.trim();
    var email   = document.getElementById('tf-email').value.trim();
    var message = document.getElementById('tf-message').value.trim();

    if (!name || !email || !message) { alert('Please fill in all required fields.'); return; }

    btn.disabled = true;
    btn.textContent = 'Sending…';

    var fd = new FormData(form);
    fd.append('action', 'threadify_contact');
    fd.append('nonce', document.getElementById('threadify_nonce').value);

    fetch('<?php echo esc_url( admin_url("admin-ajax.php") ); ?>', {
      method: 'POST',
      body: fd
    })
    .then(function (r) { return r.json(); })
    .then(function (resp) {
      if (resp.success) {
        btn.textContent = 'Sent ✓';
        form.reset();
        resetUpload();
        document.getElementById('tf-success').classList.add('visible');
      } else {
        btn.disabled = false;
        btn.textContent = 'Send Message';
        alert(resp.data || 'Something went wrong. Please try again.');
      }
    })
    .catch(function () {
      btn.disabled = false;
      btn.textContent = 'Send Message';
      alert('Network error – please try again.');
    });
  });
});
</script>

</body>
</html>
