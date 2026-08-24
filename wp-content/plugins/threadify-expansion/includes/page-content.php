<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function tse_cta_btn( $text = 'Get a Free Quote', $extra_class = '' ) {
    return '<a href="' . home_url( '/#contact' ) . '" class="tse-btn ' . $extra_class . '">' . $text . '</a>';
}

function tse_faq( $items ) {
    $html = '<div class="tse-faq">';
    foreach ( $items as $q => $a ) {
        $html .= '<div class="tse-faq-item">
            <button class="tse-faq-q">' . esc_html( $q ) . '<span class="tse-faq-icon">+</span></button>
            <div class="tse-faq-a"><p>' . wp_kses_post( $a ) . '</p></div>
        </div>';
    }
    return $html . '</div>';
}

function tse_process( $steps ) {
    $html = '<div class="tse-steps">'; $n = 1;
    foreach ( $steps as $title => $desc ) {
        $html .= '<div class="tse-step"><div class="tse-step-num">' . $n . '</div>
            <div class="tse-step-body"><h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $desc ) . '</p></div></div>';
        $n++;
    }
    return $html . '</div>';
}

function tse_icon_grid( $items ) {
    $html = '<div class="tse-icon-grid">';
    foreach ( $items as $icon => $data ) {
        $html .= '<div class="tse-icon-card"><div class="tse-icon-card-icon">' . $icon . '</div>
            <h3>' . esc_html( $data[0] ) . '</h3><p>' . esc_html( $data[1] ) . '</p></div>';
    }
    return $html . '</div>';
}

function tse_related( $links ) {
    $html = '<div class="tse-related"><h2>Related Services</h2><div class="tse-related-grid">';
    foreach ( $links as $url => $label ) {
        $html .= '<a href="' . esc_url( home_url( $url ) ) . '" class="tse-related-card">' . esc_html( $label ) . '</a>';
    }
    return $html . '</div></div>';
}

/* ── EMBROIDERY MAIN ─────────────────────────────── */
function tse_content_embroidery_main() {
    return '
<div class="tse-hero">
  <h1>Custom Embroidery in Seattle, WA</h1>
  <p>Premium embroidery for businesses, teams, and brands across the greater Seattle area. From logo digitizing to final stitch &mdash; crisp edges, lasting quality.</p>
  ' . tse_cta_btn('Get a Free Quote') . '
</div>
<div class="tse-section">
  <h2>What We Embroider</h2>
  <p>If you can wear it, we can most likely embroider it. Threadify works with hats, polos, jackets, hoodies, beanies, bags, aprons, and more. Our commercial-grade equipment handles everything from delicate left-chest logos to large back designs.</p>
  ' . tse_icon_grid([
    '🧢' => ['Hats &amp; Caps',    'Structured and unstructured caps, trucker hats, beanies, visors.'],
    '👕' => ['Polo Shirts',         'Left-chest and sleeve logos on cotton/poly and moisture-wicking fabrics.'],
    '🧥' => ['Jackets',             'Chest, back, and sleeve placements on softshells, fleece, and quilted styles.'],
    '👚' => ['Hoodies',             'Clean stitching on heavyweight fleece for team kits and branded merch.'],
    '🎽' => ['Team Apparel',        'Bulk orders with consistent placement for sports teams and clubs.'],
    '💼' => ['Corporate Workwear',  'Uniform programs, name patches, and logo embroidery for professional settings.'],
    '🎒' => ['Bags',                'Backpacks and totes — embroidery that stays clean even with heavy use.'],
    '🍽️' => ['Aprons',             'Restaurant and hospitality apparel that represents your brand.'],
  ]) . '
</div>
<div class="tse-section tse-alt">
  <h2>Why Threadify?</h2>
  ' . tse_icon_grid([
    '✅' => ['Quality Guarantee',    'Every piece is inspected before it leaves. If something is not right, we make it right.'],
    '⚡' => ['Fast Turnaround',      'Standard orders ship within 5-10 business days. Rush available.'],
    '🧵' => ['In-House Digitizing',  'We digitize your logo ourselves, controlling quality from artwork to final stitch.'],
    '📦' => ['No Real Minimum',      'Need 1 piece or 500? Both are welcome.'],
    '🎨' => ['Pantone Matching',     'We match thread colors to your brand guidelines as closely as possible.'],
    '💬' => ['Clear Communication',  'Quotes within 24 hours. You always know where your order stands.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>How It Works</h2>
  ' . tse_process([
    'Send Your Artwork'    => 'Email your logo in any format. We will advise on what works best for embroidery.',
    'We Digitize It'       => 'Our in-house team converts your design into a stitch file optimized for your garment and size.',
    'You Approve'          => 'We send a digital proof before running the full order.',
    'We Produce and Deliver' => 'Once approved, we embroider, inspect every piece, and ship or arrange pickup.',
  ]) . '
  ' . tse_cta_btn('Start Your Order') . '
</div>
<div class="tse-section tse-alt">
  <h2>Embroidery Service Pages</h2>
  ' . tse_related([
    '/embroidery/logo-embroidery/'          => 'Logo Embroidery',
    '/embroidery/embroidered-polo-shirts/'  => 'Embroidered Polo Shirts',
    '/embroidery/embroidered-jackets/'      => 'Embroidered Jackets',
    '/embroidery/embroidered-hats-caps/'    => 'Embroidered Hats & Caps',
    '/embroidery/embroidered-beanies/'      => 'Embroidered Beanies',
    '/embroidery/embroidered-hoodies/'      => 'Embroidered Hoodies',
  ]) . '
</div>
<div class="tse-section">
  <h2>Frequently Asked Questions</h2>
  ' . tse_faq([
    "What's the minimum order for embroidery?"  => 'No hard minimum. Single pieces welcome. Caps are an exception and typically come in boxes of 12 or 24.',
    'Do you keep my digitized design on file?'   => 'Yes. Once your logo is digitized, we save it for easy, fast reorders.',
    'What file formats do you accept?'           => 'Any format: JPG, PNG, PDF, AI, EPS, SVG, PSD. Vector (AI, EPS, SVG) tends to produce the cleanest results.',
    'How long does a standard order take?'       => 'Most orders ship within 5-10 business days after art approval. Rush available.',
    'Can I supply my own garments?'              => 'Yes. We accept customer-supplied items. A spoilage waiver is required.',
    'How do I care for embroidered apparel?'     => 'Turn inside out, wash cold or warm, tumble dry low. Avoid bleach and do not iron directly on embroidery.',
  ]) . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Ready to Get Started?</h2>
  <p>Send us your logo and garment details. We reply within 24 hours.</p>
  ' . tse_cta_btn('Get a Free Quote','tse-btn--lg') . '
  <p class="tse-sub">📧 <a href="mailto:Orders@ThreadifyApparel.com">Orders@ThreadifyApparel.com</a> &nbsp;|&nbsp; 📞 <a href="tel:+12532491545">(253) 249-1545</a></p>
</div>';
}

/* ── LOGO EMBROIDERY ───────────────────────────────── */
function tse_content_logo_embroidery() {
    return '
<div class="tse-hero">
  <h1>Logo Embroidery in Seattle, WA</h1>
  <p>Turn your brand logo into precision embroidery that looks sharp on any garment. In-house digitizing, clean results, and designs that last through hundreds of washes.</p>
  ' . tse_cta_btn() . '
</div>
<div class="tse-section">
  <h2>What Is Logo Embroidery?</h2>
  <p>Logo embroidery is the process of converting your brand artwork into a stitch file and sewing it directly into fabric using a commercial embroidery machine. The result is a raised, textured logo that signals quality, lasts through hundreds of washes, and never fades, cracks, or peels.</p>
  <p>Unlike printed logos, embroidery is three-dimensional with weight and texture. It is the go-to choice for corporate uniforms, team kits, and premium branded merchandise.</p>
</div>
<div class="tse-section tse-alt">
  <h2>What We Embroider Your Logo On</h2>
  ' . tse_icon_grid([
    '🧢' => ['Caps &amp; Hats', 'Front panel, side, and back placements on trucker hats, dad caps, beanies, and visors.'],
    '👕' => ['Polo Shirts',     'Left chest standard. Also right chest, sleeves, and back yoke.'],
    '🧥' => ['Jackets',         'Chest, back, and sleeve logos on softshells, fleece, and outerwear.'],
    '👚' => ['Hoodies',         'Team merch and company swag with consistent bulk placement.'],
    '🎒' => ['Bags',            'Side panels and front pockets — embroidery that holds up daily.'],
    '🍽️' => ['Aprons',         'Restaurants, breweries, cafes, and hospitality businesses.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>Placement Options</h2>
  ' . tse_icon_grid([
    '◀' => ['Left Chest',    'Standard for uniforms and corporate polos. Typically 3-4 inches wide.'],
    '▶' => ['Right Chest',   'Name tags or secondary logos alongside a left-chest brand mark.'],
    '▼' => ['Back Center',   'Larger logos on jackets and hoodies for high visibility.'],
    '●' => ['Sleeve',        'Flag patches, secondary branding, or team numbers.'],
    '▲' => ['Cap Front',     'Centered on the structured panel, typically 2-3 inches wide.'],
    '○' => ['Cap Side',      'Secondary marks or initials alongside the main front design.'],
  ]) . '
</div>
<div class="tse-section tse-alt">
  <h2>The Digitizing Process</h2>
  ' . tse_process([
    'You Send Artwork'           => 'Any format works. Vector files (AI, EPS, SVG) are ideal.',
    'We Digitize In-House'       => 'Our team converts your logo into an embroidery stitch file optimized for your garment.',
    'Digital Proof Sent'         => 'You approve the stitch file simulation before any sewing begins.',
    'Sew-Out Sample (Optional)'  => 'For new logos, we can sew a physical sample for final approval.',
    'Full Production'            => 'We run your full order and inspect every piece before shipping.',
  ]) . '
</div>
<div class="tse-section">
  <h2>Frequently Asked Questions</h2>
  ' . tse_faq([
    'How detailed can my logo be?'             => 'Most logos work well. Very fine detail or small text under 4mm tall may need simplification. We will flag any concerns during digitizing.',
    'Do you match Pantone thread colors?'      => 'Yes. We match to PMS codes when provided using our thread library.',
    'How many colors can a logo have?'         => 'Embroidery handles multi-color designs well. No strong cost penalty per color.',
    'What does digitizing cost?'               => 'A one-time setup fee based on design complexity. Reorders use the same file at no extra cost.',
  ]) . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Get Your Logo Embroidered</h2>
  ' . tse_cta_btn('Request a Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/embroidery/'                        => 'Custom Embroidery Overview',
    '/embroidery/embroidered-polo-shirts/'=> 'Embroidered Polo Shirts',
    '/embroidery/embroidered-hats-caps/'  => 'Embroidered Hats',
    '/dtf-printing/'                      => 'DTF Printing',
]);
}

/* ── POLO SHIRTS ────────────────────────────────── */
function tse_content_polo_shirts() {
    return '
<div class="tse-hero">
  <h1>Embroidered Polo Shirts in Seattle, WA</h1>
  <p>Custom embroidered polos for businesses, teams, and corporate uniforms. Clean, professional, and built to last.</p>
  ' . tse_cta_btn() . '
</div>
<div class="tse-section">
  <h2>Why Embroidered Polos?</h2>
  <p>Embroidered polo shirts are the gold standard for professional uniforms and corporate apparel. The raised texture of embroidery signals quality in a way printed logos cannot. When your team wears consistent, well-branded polo shirts, you look organized and established at trade shows, job sites, and front counters alike.</p>
  ' . tse_icon_grid([
    '👔' => ['Professional Image',    'Embroidery on a polo reads as intentional and high-quality.'],
    '🔁' => ['Wash-Proof Durability', 'Embroidery does not fade, crack, or peel after dozens of washes.'],
    '🎨' => ['Brand Color Matching',  'We match thread to your Pantone colors for consistency across the team.'],
    '📦' => ['Bulk Order Ready',      'Need 10 shirts or 300? We handle bulk orders with consistent placement.'],
  ]) . '
</div>
<div class="tse-section tse-alt">
  <h2>Popular Polo Brands</h2>
  <ul class="tse-list">
    <li><strong>Port Authority</strong> — Reliable, cost-effective, wide size range.</li>
    <li><strong>Sport-Tek</strong> — Moisture-wicking performance fabrics for outdoor roles.</li>
    <li><strong>Bella+Canvas</strong> — Softer, fashion-forward cut for retail or hospitality.</li>
    <li><strong>Carhartt</strong> — Durable workwear polos built for job site conditions.</li>
    <li><strong>Nike &amp; OGIO</strong> — Premium options for corporate gifting and exec teams.</li>
    <li><strong>Cutter &amp; Buck</strong> — High-end performance fabrics for professional teams.</li>
  </ul>
</div>
<div class="tse-section">
  <h2>How to Order</h2>
  ' . tse_process([
    'Choose Your Polo' => 'Tell us the brand, style, color, and size breakdown.',
    'Send Your Logo'   => 'Email artwork in any format. Vector preferred.',
    'Approve the Proof'=> 'Digital proof showing logo placement and thread colors.',
    'We Produce'       => 'Embroider, inspect, and ship within 5-10 business days.',
  ]) . '
  ' . tse_cta_btn('Get a Quote for Polo Shirts') . '
</div>
<div class="tse-section tse-alt">
  <h2>FAQs</h2>
  ' . tse_faq([
    'What size should my logo be for a polo?' => 'Left-chest logos typically look best at 3-4 inches wide.',
    'Can I order multiple colors with the same logo?' => 'Absolutely. Provide a size/color breakdown and we run everything together.',
    'Do you have a minimum order quantity?'   => 'No hard minimum. Volume pricing at 12+, 24+, and 50+ pieces.',
  ]) . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Order Your Custom Embroidered Polos</h2>
  ' . tse_cta_btn('Get a Free Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/embroidery/'                      => 'All Embroidery Services',
    '/embroidery/embroidered-jackets/'  => 'Embroidered Jackets',
    '/embroidery/embroidered-hats-caps/'=> 'Embroidered Hats',
    '/dtf-printing/'                    => 'DTF Printing',
]);
}

/* ── JACKETS ─────────────────────────────────────── */
function tse_content_jackets() {
    return '
<div class="tse-hero">
  <h1>Custom Embroidered Jackets in Seattle, WA</h1>
  <p>Branded jackets that actually look good. Softshells, fleece, quilted vests, and workwear jackets embroidered with clean stitching that holds up in real PNW conditions.</p>
  ' . tse_cta_btn() . '
</div>
<div class="tse-section">
  <h2>Why Embroider Your Jackets?</h2>
  <p>A well-branded jacket is the most visible piece of branded apparel your team can wear. Construction crews in softshells, real estate teams in quarter-zips, hospitality staff in fleece vests — embroidered jackets make your brand look polished year-round. Embroidery holds up to the PNW outdoor lifestyle and never peels or cracks.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Jacket Types We Embroider</h2>
  ' . tse_icon_grid([
    '🧥' => ['Softshell Jackets', 'Wind-resistant and professional. Most popular for corporate branding.'],
    '🔴' => ['Fleece Jackets',    'Comfortable and casual. Great for team kits and outdoor staff.'],
    '❄️' => ['Puffer &amp; Quilted', 'Full insulation for cold-weather work or outdoor events.'],
    '🦺' => ['Vests &amp; Half-Zips', 'Popular for real estate, healthcare, and corporate settings.'],
    '🔶' => ['Hi-Vis Jackets',   'Safety-rated outerwear for construction and industrial work.'],
    '🎽' => ['Track Jackets',    'Team jackets for sports clubs and athletic programs.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>Popular Jacket Brands</h2>
  <ul class="tse-list">
    <li><strong>Port Authority</strong> — Excellent value, wide range of styles.</li>
    <li><strong>Carhartt</strong> — Built for tough conditions, popular with trades crews.</li>
    <li><strong>The North Face</strong> — Premium branding for corporate gifts and exec teams.</li>
    <li><strong>Eddie Bauer</strong> — PNW-native brand, great for outdoor companies.</li>
    <li><strong>Cutter &amp; Buck</strong> — Professional styling for corporate programs.</li>
  </ul>
</div>
<div class="tse-section tse-alt">
  <h2>How to Order</h2>
  ' . tse_process([
    'Choose Your Style' => 'Pick from our catalog or tell us the brand and style.',
    'Send Your Logo'    => 'Any file format. We digitize in-house for best results.',
    'Approve Proof'     => 'Digital proof with placement shown. Physical sew-out available.',
    'We Deliver'        => '5-10 business days. Rush available.',
  ]) . '
  ' . tse_cta_btn('Get a Jacket Quote') . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Outfit Your Team with Branded Jackets</h2>
  ' . tse_cta_btn('Request a Free Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/embroidery/'                          => 'All Embroidery Services',
    '/embroidery/embroidered-polo-shirts/'  => 'Embroidered Polo Shirts',
    '/embroidery/embroidered-hoodies/'      => 'Embroidered Hoodies',
    '/dtf-printing/'                        => 'DTF Printing',
]);
}

/* ── HATS & CAPS ───────────────────────────────── */
function tse_content_hats() {
    return '
<div class="tse-hero">
  <h1>Embroidered Hats &amp; Caps in Seattle, WA</h1>
  <p>Custom embroidered caps for businesses, teams, events, and brands. Trucker hats, snapbacks, dad caps — all with clean, tight embroidery that holds its shape.</p>
  ' . tse_cta_btn() . '
</div>
<div class="tse-section">
  <h2>Hat Embroidery That Looks Right</h2>
  <p>Embroidering hats requires different digitizing techniques than flat garments. The curved surface and structured panels must be accounted for so logos sit flat, stitch cleanly, and look sharp from across the room. At Threadify, we digitize all cap logos in-house, specifically for the hat style being used.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Cap Styles We Embroider</h2>
  ' . tse_icon_grid([
    '🧢' => ['Structured Snapbacks',  'Six-panel caps with a firm front. Most common for bold logo placement.'],
    '🎩' => ['Unstructured Dad Caps', 'Relaxed, low-profile fit. Popular for lifestyle brands.'],
    '🚛' => ['Trucker Hats',          'Mesh back, foam front panel. Great for outdoor brands and events.'],
    '🎿' => ['Beanies',               'Knit hats for winter branded merch and PNW year-round wear.'],
    '👒' => ['Visors',                'Open-top sun visors for outdoor, golf, and athletic programs.'],
    '⛑️' => ['Fitted Caps',           'Fitted and stretch-fit options for a more premium look.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>Popular Hat Brands</h2>
  <ul class="tse-list">
    <li><strong>Richardson</strong> — The industry standard. Hundreds of styles and colors.</li>
    <li><strong>Yupoong / Flexfit</strong> — Premium structured look, widely popular.</li>
    <li><strong>Port Authority</strong> — Value-tier with good consistency for large orders.</li>
    <li><strong>Carhartt</strong> — Workwear caps that match their jacket and shirt lineup.</li>
    <li><strong>New Era</strong> — Premium option for sports and lifestyle brands.</li>
  </ul>
</div>
<div class="tse-section tse-alt">
  <h2>FAQs</h2>
  ' . tse_faq([
    'How wide should my logo be for a cap?'    => 'Front panel logos look best at 2.5-3.5 inches wide. We will advise during digitizing.',
    'Can I mix cap styles in one order?'       => 'Yes. Same logo, different styles and colors — all welcome in one order.',
    'What is the minimum for embroidered hats?' => 'No hard minimum. Volume pricing applies at 12, 24, and 48+ pieces.',
  ]) . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Order Custom Embroidered Hats</h2>
  ' . tse_cta_btn('Get a Hat Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/embroidery/'                     => 'All Embroidery Services',
    '/embroidery/embroidered-beanies/' => 'Embroidered Beanies',
    '/embroidery/logo-embroidery/'     => 'Logo Embroidery',
    '/dtf-printing/'                   => 'DTF Printing',
]);
}

/* ── BEANIES ─────────────────────────────────────── */
function tse_content_beanies() {
    return '
<div class="tse-hero">
  <h1>Embroidered Beanies in Seattle, WA</h1>
  <p>Custom embroidered beanies for crews, teams, brands, and events. Knit-friendly stitching that holds clean even on stretchy fabric.</p>
  ' . tse_cta_btn() . '
</div>
<div class="tse-section">
  <h2>Beanies Are a Pacific Northwest Staple</h2>
  <p>Seattle crews wear beanies most of the year. A well-branded beanie is one of the most practical pieces of branded apparel you can give your team or sell as merch. Embroidering on knit fabric requires specific digitizing to avoid distortion. We have developed our technique specifically for knit materials so logos stay clean even when the beanie is stretched to fit.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Common Use Cases</h2>
  <ul class="tse-list">
    <li><strong>Construction &amp; Trades Crews</strong> — Keep workers warm and branded on job sites.</li>
    <li><strong>Outdoor &amp; Recreation Brands</strong> — A natural fit for PNW outdoor lifestyle brands.</li>
    <li><strong>Restaurants &amp; Breweries</strong> — Sell or gift branded beanies to regulars and staff.</li>
    <li><strong>Sports Teams &amp; Clubs</strong> — Add a cold-weather option to your uniform program.</li>
    <li><strong>Corporate Gifts</strong> — A practical gift for clients in Seattle winters.</li>
  </ul>
</div>
<div class="tse-section">
  <h2>How to Order</h2>
  ' . tse_process([
    'Choose Your Beanie' => 'Style, color, quantity. We source from Port Authority, Richardson, and others.',
    'Send Your Logo'     => 'Any format. We digitize specifically for knit fabric.',
    'Approve the Proof'  => 'Digital simulation before production. Sew-out for larger orders.',
    'We Ship'            => 'Ready in 5-10 business days standard.',
  ]) . '
  ' . tse_cta_btn('Order Custom Beanies') . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Get Branded Beanies for Your Crew</h2>
  ' . tse_cta_btn('Request a Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/embroidery/embroidered-hats-caps/' => 'Embroidered Hats',
    '/embroidery/'                        => 'All Embroidery Services',
    '/dtf-printing/'                      => 'DTF Printing',
]);
}

/* ── HOODIES ─────────────────────────────────────── */
function tse_content_hoodies() {
    return '
<div class="tse-hero">
  <h1>Embroidered Hoodies &amp; Sweatshirts in Seattle, WA</h1>
  <p>Custom embroidered hoodies for teams, brands, and businesses. Chest, sleeve, and back placements. Clean stitching that survives heavy wash cycles season after season.</p>
  ' . tse_cta_btn() . '
</div>
<div class="tse-section">
  <h2>Why Embroidered Hoodies?</h2>
  <p>Hoodies are the most worn piece of clothing in the Pacific Northwest. A well-branded embroidered hoodie is a walking advertisement for your business. Embroidered logos will not crack, fade, or peel even after years of regular washing, making them far more durable than screen-printed or heat-transfer alternatives.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Hoodie Styles We Work With</h2>
  ' . tse_icon_grid([
    '👚' => ['Pullover Hoodies',  'The classic. Left-chest or center-chest logo, huge range of weights and brands.'],
    '🔃' => ['Full-Zip Hoodies',  'Professional look. Logo above the zipper on the left chest.'],
    '🎽' => ['Crewneck Sweatshirts', 'Clean workwear-ready alternative. Great for uniforms.'],
    '❄️' => ['Sherpa &amp; Lined', 'Premium fleece-lined options. Great for corporate gifts.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>Popular Hoodie Brands</h2>
  <ul class="tse-list">
    <li><strong>Gildan</strong> — Affordable, heavy-weight, great for high-volume orders.</li>
    <li><strong>Bella+Canvas</strong> — Softer, fashion-forward cut for restaurant and retail staff.</li>
    <li><strong>Champion</strong> — Retro vibes with solid quality. Trending for branded merch.</li>
    <li><strong>Independent Trading Co.</strong> — Premium feel for brands that want something above average.</li>
    <li><strong>Port &amp; Company</strong> — Durable and consistent workwear hoodie.</li>
  </ul>
</div>
<div class="tse-section tse-alt">
  <h2>How to Order</h2>
  ' . tse_process([
    'Pick Your Style'     => 'Brand, style, color, and size range.',
    'Submit Artwork'      => 'Any format. We digitize in-house for best result on fleece.',
    'Approve the Proof'   => 'Visual proof before any production begins.',
    'We Embroider &amp; Ship' => '5-10 business days. Rush available.',
  ]) . '
  ' . tse_cta_btn('Get a Hoodie Quote') . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Start Your Custom Hoodie Order</h2>
  ' . tse_cta_btn('Request a Free Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/embroidery/'                          => 'All Embroidery Services',
    '/embroidery/embroidered-jackets/'      => 'Embroidered Jackets',
    '/embroidery/embroidered-polo-shirts/'  => 'Embroidered Polo Shirts',
    '/dtf-printing/'                        => 'DTF Printing',
]);
}

/* ── DTF MAIN ───────────────────────────────────── */
function tse_content_dtf_main() {
    return '
<div class="tse-hero">
  <h1>DTF Printing in Seattle, WA</h1>
  <p>Direct-to-Film (DTF) transfers that print full color on virtually any fabric. No minimums, no color limits, no pretreatment. Vibrant prints that hold up wash after wash.</p>
  ' . tse_cta_btn('Get a DTF Quote') . '
</div>
<div class="tse-section">
  <h2>What Is DTF Printing?</h2>
  <p>DTF (Direct to Film) prints your design onto a special film, coats it with hot-melt adhesive powder, cures it, and heat-presses it onto the garment. It works on any fabric type — cotton, polyester, nylon, blends, and more — in any quantity from one piece to thousands.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Benefits of DTF Printing</h2>
  ' . tse_icon_grid([
    '🎨' => ['Full Color Printing', 'Unlimited colors, gradients, photographic detail — no color-count pricing.'],
    '🧶' => ['Any Fabric',          'Cotton, polyester, nylon, blends. DTF is fabric-agnostic.'],
    '1️⃣' => ['No Minimums',         'One piece or 500 — same quality either way.'],
    '⚡' => ['Fast Turnaround',     'Standard DTF orders turn around in 3-7 business days.'],
    '🔁' => ['Wash Durability',     'Flexible and wash-resistant. No cracking or peeling with normal care.'],
    '💰' => ['No Setup Fees',       'Pricing is based on print size and quantity — straightforward.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>DTF vs. Other Print Methods</h2>
  <table class="tse-table">
    <thead><tr><th>Feature</th><th>DTF</th><th>Screen Print</th><th>Embroidery</th></tr></thead>
    <tbody>
      <tr><td>Color Count</td><td>Unlimited</td><td>Limited</td><td>Limited</td></tr>
      <tr><td>Fabric Types</td><td>Any</td><td>Most</td><td>Most</td></tr>
      <tr><td>Min Order</td><td>1 piece</td><td>12+ typical</td><td>1 piece</td></tr>
      <tr><td>Photo / Gradient</td><td>Yes</td><td>No</td><td>No</td></tr>
      <tr><td>Setup Cost</td><td>None</td><td>Per screen</td><td>Digitizing fee</td></tr>
    </tbody>
  </table>
</div>
<div class="tse-section tse-alt">
  <h2>How DTF Printing Works</h2>
  ' . tse_process([
    'Submit Your Design'    => 'PNG with transparent background or any format. We prepare the print-ready file.',
    'We Print the Transfer' => 'Your design is printed onto DTF film using CMYK + white inks.',
    'Powder and Cure'       => 'Hot-melt adhesive powder applied and cured — ready for pressing.',
    'Heat Press Transfer'   => 'Pressed onto your garment at precise temperature and pressure.',
    'Quality Check and Ship'=> 'We inspect every piece. Orders ready in 3-7 business days.',
  ]) . '
  ' . tse_cta_btn('Start Your DTF Order') . '
</div>
<div class="tse-section">
  <h2>DTF Printing FAQs</h2>
  ' . tse_faq([
    'Is there a minimum order for DTF?'          => 'No minimum. Single pieces welcome.',
    'What file format do you need?'              => 'PNG with transparent background is ideal. We also accept PSD, AI, PDF, and high-res JPG.',
    'Does DTF work on dark shirts?'              => 'Yes. DTF uses a white ink underbase for vibrant colors on dark and black garments.',
    'How long do DTF prints last?'               => 'With proper care, 50+ wash cycles without significant fading.',
    'Can I order gang sheets?'                   => 'Yes. Pack multiple designs onto a single transfer sheet to reduce cost per design.',
  ]) . '
</div>
<div class="tse-section tse-alt">
  <h2>Explore DTF Services</h2>
  ' . tse_related([
    '/dtf-printing/custom-t-shirts/' => 'Custom DTF T-Shirts',
    '/dtf-printing/dtf-for-teams/'   => 'DTF for Teams',
    '/dtf-printing/gang-sheets/'     => 'Gang Sheet Transfers',
    '/embroidery/'                    => 'Custom Embroidery',
  ]) . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Ready to Print?</h2>
  <p>Send your design and garment details. We reply with pricing within 24 hours.</p>
  ' . tse_cta_btn('Get a Free Quote','tse-btn--lg') . '
  <p class="tse-sub">📧 <a href="mailto:Orders@ThreadifyApparel.com">Orders@ThreadifyApparel.com</a> &nbsp;|&nbsp; 📞 <a href="tel:+12532491545">(253) 249-1545</a></p>
</div>';
}

/* ── DTF T-SHIRTS ───────────────────────────────── */
function tse_content_dtf_tshirts() {
    return '
<div class="tse-hero">
  <h1>Custom DTF Printed T-Shirts in Seattle, WA</h1>
  <p>Full-color custom t-shirts with DTF printing. Any design, any fabric color, any quantity. No minimums, no color restrictions, no setup fees per color.</p>
  ' . tse_cta_btn('Get a T-Shirt Quote') . '
</div>
<div class="tse-section">
  <h2>Why DTF for Custom T-Shirts?</h2>
  <p>DTF is the most flexible way to print custom t-shirts. No minimum order, no per-color screen fee, and it works on any fabric type including polyester and dark shirts without pretreatment. If you want full-color, gradient, or photographic designs, DTF is the right method.</p>
</div>
<div class="tse-section tse-alt">
  <h2>T-Shirt Brands We Source</h2>
  <ul class="tse-list">
    <li><strong>Bella+Canvas 3001</strong> — Super-soft ring-spun cotton. Excellent print surface.</li>
    <li><strong>Gildan 64000</strong> — Great value ring-spun cotton. Workhorse of custom printing.</li>
    <li><strong>Next Level Apparel</strong> — Fashion-forward fit. Popular for merch and events.</li>
    <li><strong>Sport-Tek</strong> — Performance polyester for athletic and outdoor applications.</li>
    <li><strong>Hanes</strong> — Affordable and reliable for large-quantity orders.</li>
  </ul>
</div>
<div class="tse-section">
  <h2>Common Use Cases</h2>
  ' . tse_icon_grid([
    '🎪' => ['Events &amp; Festivals', 'Any quantity from single VIP tees to hundreds for attendees.'],
    '🏆' => ['Sports Teams',        'Team shirts with numbers, names, or just the team logo.'],
    '🍺' => ['Restaurants &amp; Breweries', 'Staff shirts, merch for regulars, event-night tees.'],
    '💼' => ['Corporate Programs',  'Trade show teams, branded shirts for staff and company events.'],
    '🎨' => ['Merch Drops',         'Small clothing brands — print limited runs without minimums.'],
    '🎁' => ['Custom Gifts',        'One-of-a-kind personalized shirts for birthdays and milestones.'],
  ]) . '
</div>
<div class="tse-section tse-alt">
  <h2>How to Order</h2>
  ' . tse_process([
    'Choose Your Blank'    => 'Brand, style, color, and sizes. We source it or you supply your own shirts.',
    'Send Your Design'     => 'PNG with transparent background is best. Any high-res file works.',
    'Review and Approve'   => 'Digital mockup showing design on your shirt. Approve before we press.',
    'We Print and Ship'    => '3-7 business days. Shirts inspected and packed carefully.',
  ]) . '
  ' . tse_cta_btn('Start Your T-Shirt Order') . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Print Custom T-Shirts Today</h2>
  <p>No minimums. Full color. Any fabric. Any quantity.</p>
  ' . tse_cta_btn('Get a Free Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/dtf-printing/'               => 'All DTF Printing Services',
    '/dtf-printing/dtf-for-teams/' => 'DTF for Teams',
    '/dtf-printing/gang-sheets/'   => 'Gang Sheet Transfers',
    '/embroidery/'                 => 'Custom Embroidery',
]);
}

/* ── DTF TEAMS ──────────────────────────────────── */
function tse_content_dtf_teams() {
    return '
<div class="tse-hero">
  <h1>DTF Printing for Teams &amp; Businesses in Seattle</h1>
  <p>Full-color custom apparel for your whole team. No minimums, fast turnaround, and consistent results across every piece.</p>
  ' . tse_cta_btn('Get a Team Quote') . '
</div>
<div class="tse-section">
  <h2>Why Teams Choose DTF</h2>
  <p>DTF removes the typical barriers of custom apparel. No screen minimums, no color-count penalties, no pretreatment requirements. Order the exact quantity you need, in multiple styles and colors, all with the same design. Easy to add names and numbers, update designs between seasons, or test a colorway on a small run.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Who We Work With</h2>
  ' . tse_icon_grid([
    '🏗️' => ['Construction &amp; Trades', 'Hi-vis tees, safety shirts, and crew uniforms.'],
    '🍔' => ['Restaurants &amp; Cafes',   'Staff tees and event shirts in any quantity. Easy reorders.'],
    '💼' => ['Corporate Teams',           'Branded shirts for trade shows, all-hands events, and milestones.'],
    '⚽' => ['Sports &amp; Recreation',  'League jerseys, rec sports shirts, club and association apparel.'],
    '🎓' => ['Schools &amp; Organizations','Spirit wear, class shirts, club tees, and fundraiser apparel.'],
    '🛒' => ['Retail &amp; Merch Brands', 'Small-run production without screen minimums.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>Easy Reordering for Growing Teams</h2>
  <p>Once your design is in our system, reorders are simple. New employees, size changes, worn-out shirts — just send us the quantity and we handle the rest. No re-setup, no minimums.</p>
  ' . tse_cta_btn('Set Up Your Team Account') . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Outfit Your Team Today</h2>
  ' . tse_cta_btn('Get a Team Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/dtf-printing/'                 => 'All DTF Printing Services',
    '/dtf-printing/custom-t-shirts/' => 'Custom DTF T-Shirts',
    '/embroidery/'                   => 'Custom Embroidery for Teams',
    '/custom-patches/'               => 'Custom Patches',
]);
}

/* ── GANG SHEETS ────────────────────────────────── */
function tse_content_gang_sheets() {
    return '
<div class="tse-hero">
  <h1>Gang Sheet DTF Transfers in Seattle, WA</h1>
  <p>Pack multiple designs onto a single transfer sheet — the most cost-effective way to produce DTF prints for small clothing brands and merch creators.</p>
  ' . tse_cta_btn('Order Gang Sheets') . '
</div>
<div class="tse-section">
  <h2>What Is a Gang Sheet?</h2>
  <p>A gang sheet is a large DTF transfer film with multiple designs packed tightly together to minimize cost per transfer. Instead of printing one design at a time, you get dozens of transfers on a single sheet. Popular with small clothing brands, Etsy sellers, boutique businesses, and anyone producing custom apparel in small batches.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Why Order Gang Sheets?</h2>
  ' . tse_icon_grid([
    '💰' => ['Lower Cost Per Design', 'Pack multiple designs and pay based on total sheet area — not per design.'],
    '🎨' => ['Mix Designs Freely',    'Different logos, colors, and sizes all on one sheet.'],
    '🏭' => ['Press Yourself',        'We ship ready-to-press transfers. You press them at your own pace.'],
    '⚡' => ['Fast Turnaround',       'Gang sheets typically ship in 2-4 business days.'],
    '📐' => ['Custom Sheet Sizes',    '22 inch wide sheets in any length to fit exactly what you need.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>How to Order</h2>
  ' . tse_process([
    'Prepare Your Artwork'    => 'PNG files with transparent background. Send multiple designs in one email.',
    'We Lay Out the Sheet'    => 'We arrange your designs efficiently to minimize waste on the film.',
    'You Approve the Layout'  => 'Digital layout proof before printing.',
    'We Print and Ship'       => 'Ready-to-press transfers shipped in 2-4 business days.',
  ]) . '
  ' . tse_cta_btn('Start Your Gang Sheet Order') . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Order DTF Gang Sheet Transfers</h2>
  ' . tse_cta_btn('Get a Transfer Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/dtf-printing/'                 => 'All DTF Printing',
    '/dtf-printing/custom-t-shirts/' => 'Custom T-Shirts',
    '/dtf-printing/dtf-for-teams/'   => 'DTF for Teams',
    '/embroidery/'                   => 'Custom Embroidery',
]);
}

/* ── DESIGN & DIGITIZING ───────────────────────── */
function tse_content_digitizing() {
    return '
<div class="tse-hero">
  <h1>Embroidery Design &amp; Digitizing in Seattle, WA</h1>
  <p>We convert logos and artwork into embroidery stitch files that sew beautifully. All digitizing is done in-house — not outsourced to automated software.</p>
  ' . tse_cta_btn('Get Your Logo Digitized') . '
</div>
<div class="tse-section">
  <h2>What Is Embroidery Digitizing?</h2>
  <p>Digitizing converts your artwork into a stitch file containing instructions for every aspect of the design: where each needle goes, what direction the stitches run, how densely they are packed, and in what order each color is sewn. A well-digitized file produces clean, non-puckering embroidery. Poor digitizing produces logos that shift, bleed, or fall apart at the edges.</p>
</div>
<div class="tse-section tse-alt">
  <h2>What We Account for When Digitizing</h2>
  ' . tse_icon_grid([
    '📐' => ['Stitch Direction', 'Thread angle affects look and texture. We select direction based on the design element.'],
    '🧱' => ['Underlay',         'Supporting stitches stabilize fabric and prevent top stitches from sinking in.'],
    '⚖️' => ['Density',          'Calibrated per fabric type to prevent buckling or show-through.'],
    '📏' => ['Pull Compensation','We compensate for the inward pull of thread so the logo comes out the correct size.'],
    '🧵' => ['Garment Type',     'Digitizing for a structured hat front differs from fleece. We optimize per application.'],
    '🎨' => ['Color Sequence',   'Efficient ordering reduces machine stops and improves run consistency.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>File Formats We Accept</h2>
  <ul class="tse-list">
    <li><strong>Vector (preferred):</strong> AI, EPS, SVG, PDF — clean, scalable artwork.</li>
    <li><strong>Raster:</strong> PNG, JPG, PSD — 300 DPI or higher required.</li>
    <li><strong>Existing embroidery files:</strong> DST, PES, JEF — we can inspect, edit, or re-digitize.</li>
  </ul>
</div>
<div class="tse-section tse-alt">
  <h2>The Digitizing Process</h2>
  ' . tse_process([
    'You Submit Artwork'     => 'Include the target garment type and desired stitch size.',
    'We Evaluate the Design' => 'We flag any elements that need simplification for embroidery.',
    'We Digitize'            => 'Manually digitized with stitch direction, underlay, density, and pull compensation.',
    'Digital Proof'          => 'We send a simulation. Physical sew-out available on request.',
    'File Saved to Account'  => 'Your file stays on file for easy reorders at no re-digitizing fee.',
  ]) . '
  ' . tse_cta_btn('Submit Your Logo') . '
</div>
<div class="tse-section">
  <h2>Stand-Alone Digitizing Service</h2>
  <p>You do not have to order garments from us to use our digitizing service. If you have your own embroidery machine, we can supply a production-ready stitch file in DST, PES, JEF, or other formats. Contact us for stand-alone digitizing pricing.</p>
</div>
<div class="tse-section tse-cta-banner">
  <h2>Get Your Logo Ready for Embroidery</h2>
  ' . tse_cta_btn('Get a Digitizing Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/embroidery/'                => 'Custom Embroidery Services',
    '/embroidery/logo-embroidery/'=> 'Logo Embroidery',
    '/dtf-printing/'              => 'DTF Printing',
]);
}

/* ── CUSTOM PATCHES ─────────────────────────────── */
function tse_content_patches() {
    return '
<div class="tse-hero">
  <h1>Custom Embroidered Patches in Seattle, WA</h1>
  <p>Iron-on, sew-on, and Velcro embroidered patches for uniforms, hats, jackets, and merchandise. Any shape, size, and thread color.</p>
  ' . tse_cta_btn('Get a Patch Quote') . '
</div>
<div class="tse-section">
  <h2>About Our Custom Patches</h2>
  <p>Custom embroidered patches can be applied to almost any garment or accessory, replaced without replacing the garment, and produced in bulk at a lower per-unit cost than direct embroidery on each item. We produce patches in-house on commercial embroidery equipment using quality polyester twill backing.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Patch Types and Options</h2>
  ' . tse_icon_grid([
    '🧵' => ['Embroidered Twill', 'Colorful thread on twill backing. Durable and professional.'],
    '🔵' => ['Merrowed Edge',     'Chain-stitched border around the patch edge for a clean finished look.'],
    '✂️' => ['Die-Cut Edge',      'Patch cut to the exact shape of the design.'],
    '🔥' => ['Iron-On Backing',   'Heat-activated adhesive — no sewing required.'],
    '🧷' => ['Sew-On Backing',    'Traditional backing for high-durability applications.'],
    '📎' => ['Velcro Backing',    'Hook-and-loop for removable patches on tactical gear and uniforms.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>Common Uses</h2>
  <ul class="tse-list">
    <li><strong>Uniform Programs</strong> — Name, rank, or company logo patches for staff uniforms.</li>
    <li><strong>Caps &amp; Hats</strong> — Patches as an alternative to direct-sew hat decoration.</li>
    <li><strong>Jackets &amp; Vests</strong> — Biker vests, workwear, and company jackets.</li>
    <li><strong>Merchandise</strong> — Patches as sellable merch for brands and organizations.</li>
    <li><strong>Sports &amp; Recreation</strong> — Team and league patches for jerseys and gear bags.</li>
  </ul>
</div>
<div class="tse-section tse-alt">
  <h2>How to Order</h2>
  ' . tse_process([
    'Send Your Artwork'  => 'Email your logo. Vector files preferred.',
    'Choose Patch Specs' => 'Size, shape, backing type, and edge style.',
    'Approve the Proof'  => 'Digital proof plus physical sample before full production.',
    'We Produce'         => 'Patches ship within 7-14 business days.',
  ]) . '
  ' . tse_cta_btn('Order Custom Patches') . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Order Your Custom Patches</h2>
  ' . tse_cta_btn('Request a Patch Quote','tse-btn--lg') . '
</div>
' . tse_related([
    '/embroidery/'         => 'Custom Embroidery',
    '/dtf-printing/'       => 'DTF Printing',
    '/design-digitizing/'  => 'Design and Digitizing',
]);
}

/* ── FAQ ────────────────────────────────────────── */
function tse_content_faq() {
    return '
<div class="tse-hero">
  <h1>Frequently Asked Questions</h1>
  <p>Everything you need to know about ordering custom embroidery and DTF printing from Threadify.</p>
</div>
<div class="tse-section">
  <h2>General Questions</h2>
  ' . tse_faq([
    'Where is Threadify located?'  => 'Based in Seattle, Washington. We serve the greater Seattle area and ship anywhere in the US. Local pickup available.',
    'What services do you offer?'  => 'Custom embroidery (logos, hats, polos, jackets, hoodies, beanies, patches), DTF printing (full-color heat transfer on any fabric), and embroidery digitizing as a standalone service.',
    'How do I get a quote?'        => 'Email Orders@ThreadifyApparel.com with your artwork and project details. We typically respond within 24 hours.',
    'Is there a minimum order?'    => 'No hard minimum for most services. A single piece is welcome. Volume pricing kicks in as quantity grows.',
    'Do you accept customer-supplied garments?' => 'Yes. A spoilage waiver is required since we cannot replace items we do not source.',
  ]) . '
</div>
<div class="tse-section tse-alt">
  <h2>Embroidery Questions</h2>
  ' . tse_faq([
    'What file format do you need for embroidery?'  => 'Any format works. Vector files (AI, EPS, SVG) are preferred. High-res PNG or JPG at 300 DPI+ also works. We digitize in-house.',
    'How long does embroidery take?'                => '5-10 business days after art approval. Rush available.',
    'Is there a setup fee for my logo?'             => 'A one-time digitizing fee applies. All reorders use the same file at no additional setup cost.',
    'Do you save my design for future orders?'      => 'Yes. Your digitized file stays on file indefinitely.',
    'What thread colors are available?'             => 'Hundreds of colors. We match to Pantone codes. Metallics available on request.',
    'How do I care for embroidered garments?'       => 'Turn inside out, wash cold or warm, tumble dry low. Avoid bleach and do not iron on the embroidery.',
  ]) . '
</div>
<div class="tse-section">
  <h2>DTF Printing Questions</h2>
  ' . tse_faq([
    'What is DTF printing?'              => 'Direct-to-Film transfers full-color designs onto any fabric via heat press. Unlimited colors, no minimums, no pretreatment.',
    'Can DTF print on dark shirts?'      => 'Yes. DTF uses a white ink underbase so colors print vibrantly on dark garments.',
    'How long do DTF prints last?'       => 'With proper care — wash inside-out cold, tumble dry low — prints last well over 50 wash cycles.',
    'What file format for DTF?'          => 'PNG with transparent background is ideal. We also accept AI, PSD, PDF, high-res JPG.',
    'Do you sell DTF transfers for self-pressing?' => 'Yes. We offer ready-to-press gang sheet transfers. Great for small clothing brands.',
  ]) . '
</div>
<div class="tse-section tse-alt">
  <h2>Ordering Questions</h2>
  ' . tse_faq([
    'Do you offer rush orders?'    => 'Yes, when capacity allows. Rush orders carry a rush fee. Contact us early if you have a hard deadline.',
    'Do you ship orders?'          => 'Yes. UPS and USPS throughout the US. Local Seattle pickup also available.',
    'Can I see a sample first?'    => 'Yes. Digital proofs before production, physical sew-out samples for embroidery on request.',
    'What if I am unhappy with my order?' => 'If something does not match the approved proof, we fix it or re-run it. Contact us within 7 days of receiving your order.',
  ]) . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Still Have Questions?</h2>
  ' . tse_cta_btn('Contact Us','tse-btn--lg') . '
  <p class="tse-sub">📧 <a href="mailto:Orders@ThreadifyApparel.com">Orders@ThreadifyApparel.com</a> &nbsp;|&nbsp; 📞 <a href="tel:+12532491545">(253) 249-1545</a></p>
</div>';
}

/* ── ABOUT ──────────────────────────────────────── */
function tse_content_about() {
    return '
<div class="tse-hero">
  <h1>About Threadify</h1>
  <p>Custom embroidery and DTF printing based in Seattle, WA. Quality work, honest pricing, and a process that does not waste your time.</p>
</div>
<div class="tse-section">
  <h2>Who We Are</h2>
  <p>Threadify is a custom apparel decoration shop operating out of Seattle, Washington. We specialize in embroidery and DTF printing for businesses, teams, organizations, and individuals across the greater Seattle area and the Pacific Northwest.</p>
  <p>We are a small operation — which means you are working directly with the people doing the work. No call centers, no runaround. If you have a question about your order, you can reach the person actually handling it.</p>
</div>
<div class="tse-section tse-alt">
  <h2>What We Believe</h2>
  ' . tse_icon_grid([
    '✅' => ['Quality Over Volume',   'We inspect every piece before it ships. If it does not look right, we do not send it.'],
    '🧵' => ['Digitizing Is Craft',   'We digitize every logo in-house because human judgment produces better results than automated software.'],
    '💬' => ['No Runaround',          'Quotes within 24 hours. Honest timelines. You will never wonder where your order stands.'],
    '📦' => ['Any Quantity Welcome',  'One piece or five hundred — every order receives the same care and attention.'],
  ]) . '
</div>
<div class="tse-section">
  <h2>Our Services</h2>
  <ul class="tse-list">
    <li><strong>Custom Embroidery</strong> — Hats, polos, jackets, hoodies, beanies, bags, and more.</li>
    <li><strong>DTF Printing</strong> — Full-color transfers for any garment, any fabric, any quantity.</li>
    <li><strong>Embroidery Digitizing</strong> — Logo conversion to production-ready stitch files.</li>
    <li><strong>Custom Patches</strong> — Embroidered patches with iron-on, sew-on, or Velcro backing.</li>
    <li><strong>Gang Sheet Transfers</strong> — Ready-to-press DTF transfers for at-home or business use.</li>
  </ul>
</div>
<div class="tse-section tse-cta-banner">
  <h2>Work With Us</h2>
  <p>Send your project details. We get back to you within 24 hours.</p>
  ' . tse_cta_btn('Get a Free Quote','tse-btn--lg') . '
  <p class="tse-sub">📧 <a href="mailto:Orders@ThreadifyApparel.com">Orders@ThreadifyApparel.com</a> &nbsp;|&nbsp; 📞 <a href="tel:+12532491545">(253) 249-1545</a><br>📍 Seattle, Washington</p>
</div>';
}

/* ── SERVICE AREAS PARENT ───────────────────────── */
function tse_content_service_areas() {
    $areas = [
        '/service-areas/seattle/'  => 'Seattle, WA',
        '/service-areas/bellevue/' => 'Bellevue, WA',
        '/service-areas/kirkland/' => 'Kirkland, WA',
        '/service-areas/renton/'   => 'Renton, WA',
        '/service-areas/tacoma/'   => 'Tacoma, WA',
        '/service-areas/redmond/'  => 'Redmond, WA',
        '/service-areas/kent/'     => 'Kent, WA',
        '/service-areas/bothell/'  => 'Bothell, WA',
    ];
    $grid = '<div class="tse-area-grid">';
    foreach ( $areas as $url => $label ) {
        $grid .= '<a href="' . esc_url( home_url( $url ) ) . '" class="tse-area-card"><span>📍</span><span>' . esc_html( $label ) . '</span></a>';
    }
    $grid .= '</div>';
    return '
<div class="tse-hero">
  <h1>Custom Embroidery &amp; DTF Printing — Seattle Area Service Locations</h1>
  <p>Threadify serves businesses, teams, and individuals across the greater Seattle metro. We also ship anywhere in Washington and the US.</p>
  ' . tse_cta_btn() . '
</div>
<div class="tse-section">
  <h2>Areas We Serve</h2>
  <p>Based in Seattle, serving clients throughout King and Pierce Counties. Fast shipping means we work with clients anywhere in the Pacific Northwest and nationwide.</p>
  ' . $grid . '
</div>
<div class="tse-section tse-alt">
  <h2>How We Work With Remote Clients</h2>
  ' . tse_icon_grid([
    '📧' => ['Easy Email Process', 'Send artwork and order details by email. We quote, proof, and confirm before production.'],
    '📦' => ['Fast Shipping',      'Orders ship via UPS or USPS. Most arrive within 2-5 business days after production.'],
    '🔁' => ['Easy Reorders',      'Your files and order history stay on file for fast, frictionless reorders.'],
    '📞' => ['Real Communication', 'Call or email anytime to check on your order.'],
  ]) . '
</div>
<div class="tse-section tse-cta-banner">
  <h2>Order from Anywhere in the Pacific Northwest</h2>
  ' . tse_cta_btn('Get a Free Quote','tse-btn--lg') . '
</div>';
}

/* ── SERVICE AREA CITY (template) ──────────────── */
function tse_content_service_area_city( $city ) {
    $c = esc_html( $city );
    return '
<div class="tse-hero">
  <h1>Custom Embroidery &amp; DTF Printing in ' . $c . '</h1>
  <p>Threadify provides premium embroidery and DTF printing to businesses and individuals in ' . $c . ' and surrounding areas. Fast turnaround, honest pricing, quality in every stitch.</p>
  ' . tse_cta_btn() . '
</div>
<div class="tse-section">
  <h2>Embroidery &amp; DTF Printing Near ' . $c . '</h2>
  <p>Whether you are a small business in ' . $c . ' looking to brand your team, a local sports organization needing bulk uniforms, or an individual wanting one custom piece — Threadify delivers quality work with a process that does not waste your time. Most orders are placed entirely by email: send your artwork, get a quote, approve a proof, and we handle the rest.</p>
</div>
<div class="tse-section tse-alt">
  <h2>Services Available in ' . $c . '</h2>
  ' . tse_related([
    '/embroidery/'                         => 'Custom Embroidery',
    '/embroidery/logo-embroidery/'         => 'Logo Embroidery',
    '/embroidery/embroidered-hats-caps/'   => 'Embroidered Hats',
    '/embroidery/embroidered-polo-shirts/' => 'Embroidered Polo Shirts',
    '/dtf-printing/'                       => 'DTF Printing',
    '/dtf-printing/custom-t-shirts/'       => 'Custom T-Shirts',
    '/custom-patches/'                     => 'Custom Patches',
    '/design-digitizing/'                  => 'Embroidery Digitizing',
  ]) . '
</div>
<div class="tse-section">
  <h2>Who We Serve in ' . $c . '</h2>
  <ul class="tse-list">
    <li><strong>Restaurants, Breweries &amp; Hospitality</strong> — Staff aprons, polos, and branded merch.</li>
    <li><strong>Construction &amp; Trades</strong> — Crew uniforms, hi-vis shirts, embroidered jackets.</li>
    <li><strong>Corporate &amp; Office Teams</strong> — Polos, quarter-zips, and branded trade show kits.</li>
    <li><strong>Sports Teams &amp; Organizations</strong> — Hats, jerseys, practice shirts, and team bags.</li>
    <li><strong>Schools &amp; Nonprofits</strong> — Event tees, spirit wear, and staff uniforms.</li>
    <li><strong>Small Businesses &amp; Startups</strong> — First-time branded apparel, no minimum required.</li>
  </ul>
</div>
<div class="tse-section tse-cta-banner">
  <h2>Get a Quote for Your ' . $c . ' Order</h2>
  ' . tse_cta_btn('Request a Free Quote','tse-btn--lg') . '
  <p class="tse-sub">📧 <a href="mailto:Orders@ThreadifyApparel.com">Orders@ThreadifyApparel.com</a> &nbsp;|&nbsp; 📞 <a href="tel:+12532491545">(253) 249-1545</a></p>
</div>
' . tse_related([
    '/service-areas/' => 'All Service Areas',
    '/faq/'           => 'FAQs',
    '/about/'         => 'About Threadify',
]);
}

/* ── SHOP ───────────────────────────────────────── */
function tse_content_shop() {
    return '
<div id="tse-shop">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Build your order</span>
      <h2>Shop blanks by industry.</h2>
      <p>Pick your industry and browse real SanMar &amp; Carhartt garments that fit it — with product photos and full color ranges. Choose your pieces, set quantities, tell us where you want your embroidery, and add your logo. We finish with a quote, no payment up front.</p>
    </div>
    <div class="ind-layout">
      <div class="ind-tabs" id="indTabs" role="tablist" aria-label="Shop by industry">
        <button type="button" class="ind-tab active" data-industry="construction" role="tab" aria-selected="true">
          <span class="ic" aria-hidden="true">&#9874;</span>
          <span class="tab-txt"><h3>Construction</h3><p>Carhartt tees, work shirts, duck jackets, hi-vis &amp; caps.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="culinary" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#127859;</span>
          <span class="tab-txt"><h3>Culinary</h3><p>Aprons, chef wear &amp; soft tees.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="office" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#128188;</span>
          <span class="tab-txt"><h3>Office &amp; Corporate</h3><p>Silk Touch polos, soft shells, fleece &amp; backpacks.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="spirit" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#127881;</span>
          <span class="tab-txt"><h3>Spirit Merch</h3><p>Soft tees, hoodies &amp; crews for teams &amp; schools.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="medical" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#9877;</span>
          <span class="tab-txt"><h3>Medical &amp; Healthcare</h3><p>Scrubs, lab coats &amp; snag-proof polos.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="hospitality" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#127870;</span>
          <span class="tab-txt"><h3>Hospitality &amp; Events</h3><p>Aprons, polos, tees &amp; totes for staff.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="fitness" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#128170;</span>
          <span class="tab-txt"><h3>Fitness &amp; Wellness</h3><p>Performance tees, polos, hoodies &amp; tanks.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="automotive" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#128295;</span>
          <span class="tab-txt"><h3>Automotive &amp; Trades</h3><p>Industrial work shirts, Carhartt &amp; hi-vis.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="outdoor" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#127956;</span>
          <span class="tab-txt"><h3>Outdoor &amp; Recreation</h3><p>The North Face, Eddie Bauer &amp; Cotopaxi.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="golf" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#9971;</span>
          <span class="tab-txt"><h3>Golf &amp; Country Club</h3><p>TravisMathew &amp; Nike performance polos.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="bags" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#127890;</span>
          <span class="tab-txt"><h3>Bags &amp; Accessories</h3><p>Totes, backpacks, duffels &amp; coolers.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="headwear" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#129504;</span>
          <span class="tab-txt"><h3>Headwear</h3><p>Structured caps, snapbacks &amp; beanies.</p></span>
        </button>
        <button type="button" class="ind-tab" data-industry="education" role="tab" aria-selected="false">
          <span class="ic" aria-hidden="true">&#127891;</span>
          <span class="tab-txt"><h3>Education &amp; Youth</h3><p>Youth &amp; toddler tees, hoodies &amp; joggers.</p></span>
        </button>
      </div>
      <a class="ind-photo-panel" id="indPhotoPanel" href="/order-builder/?industry=construction">
        <img id="indPhotoImg" src="https://threadifyapparel.com/wp-content/uploads/2026/07/construction-pic.jpg" alt="Crew working on a construction site in Carhartt-style workwear" />
        <div class="ind-photo-cap">
          <div class="pt" id="indPhotoTitle">Construction</div>
          <div class="go" id="indPhotoGo">Browse Construction garments →</div>
        </div>
      </a>
    </div>
    <p class="shop-note">Nearly 3,000 real SanMar garments across 13 industries, each with its full real color range and in-depth specs — pick an industry, search or browse, and click any photo for full details. Exact sizes and availability are confirmed with your quote.</p>
  </div>

  <div class="wrap" style="margin-top:64px;">
    <div class="section-head">
      <span class="eyebrow">Recent work</span>
      <h2>Real orders, off our machines.</h2>
      <p>A look at pieces we\'ve made for brands, schools, teams, and small businesses around Federal Way and the Puget Sound area. Use the filters to browse by method.</p>
    </div>

    <div class="filters" id="filters">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="embroidery">Embroidery</button>
      <button class="filter-btn" data-filter="dtf">DTF Printing</button>
      <button class="filter-btn" data-filter="patches">Custom Patches</button>
      <button class="filter-btn" data-filter="heatpress">Heat Press</button>
    </div>

    <div class="gallery" id="gallery">
      <figure class="tile work-item" data-category="embroidery">
        <img src="https://threadifyapparel.com/wp-content/uploads/2026/07/threadify-pics1.jpg" alt="Black hoodies with embroidered Salesforce logos" loading="lazy" />
        <figcaption class="cap"><div class="t">Salesforce team hoodies</div><div class="s">Embroidery</div></figcaption>
      </figure>
      <figure class="tile work-item" data-category="embroidery">
        <img src="https://threadifyapparel.com/wp-content/uploads/2026/07/threadify-pics4.jpg" alt="Gray quarter-zip embroidered with UW School of Pharmacy logo" loading="lazy" />
        <figcaption class="cap"><div class="t">UW School of Pharmacy 1/4-zip</div><div class="s">Embroidery</div></figcaption>
      </figure>
      <figure class="tile work-item" data-category="embroidery">
        <img src="https://threadifyapparel.com/wp-content/uploads/2026/07/threadify-pics2.jpg" alt="Embroidery machine stitching a Kappa Psi Greek-letter design" loading="lazy" />
        <figcaption class="cap"><div class="t">Kappa Psi Greek-letter crest</div><div class="s">Embroidery</div></figcaption>
      </figure>
      <figure class="tile work-item" data-category="embroidery">
        <img src="https://threadifyapparel.com/wp-content/uploads/2026/07/threadify-pics3.jpg" alt="Embroidery machine stitching a detailed dragon design" loading="lazy" />
        <figcaption class="cap"><div class="t">Custom dragon artwork</div><div class="s">Embroidery</div></figcaption>
      </figure>
      <figure class="tile work-item" data-category="embroidery">
        <img src="https://threadifyapparel.com/wp-content/uploads/2026/07/threadify-pics5.jpg" alt="Canvas tote bag embroidered with the Red Wagon Burger logo" loading="lazy" />
        <figcaption class="cap"><div class="t">Red Wagon Burger tote</div><div class="s">Embroidery</div></figcaption>
      </figure>
      <figure class="tile work-item" data-category="embroidery">
        <img src="https://threadifyapparel.com/wp-content/uploads/2026/07/threadify-pics6.jpg" alt="Embroidered USA World Cup totes hanging at a Threadify market booth" loading="lazy" />
        <figcaption class="cap"><div class="t">World Cup market totes</div><div class="s">Embroidery</div></figcaption>
      </figure>

      <!-- Empty-state shown for methods we have not photographed yet (no fake samples). -->
      <div class="empty-state hide" id="emptyState">
        <h3>Samples coming soon</h3>
        <p>We do this work every week — we just haven\'t added photos to this filter yet. Want to see examples? Reach out and we\'ll send recent pieces.</p>
        <a href="/#contact" class="btn btn-primary">Request samples</a>
      </div>
    </div>
  </div>
</div>
<script>
(function () {
  "use strict";

  var INDUSTRY_PHOTOS = {
    construction: { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/construction-pic.jpg", alt: "Crew working on a construction site in Carhartt-style workwear", name: "Construction" },
    culinary:     { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/culinary-pic.jpg", alt: "Chef in an embroidered apron dusting flour over fresh pasta in a restaurant kitchen", name: "Culinary" },
    office:       { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/office-pic.jpg", alt: "Team in coordinated business-casual embroidered apparel", name: "Office & Corporate" },
    spirit:       { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/spirit-pic.jpg", alt: "Group in matching embroidered team hoodies", name: "Spirit Merch" },
    medical:      { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/healthcare-pic.jpg", alt: "Two healthcare workers in teal scrubs", name: "Medical & Healthcare" },
    hospitality:  { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/hospitality-pic.jpg", alt: "Restaurant front-of-house staff in embroidered aprons and uniforms", name: "Hospitality & Events" },
    fitness:      { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/fitness-pic.jpg", alt: "Person in athletic performance apparel", name: "Fitness & Wellness" },
    automotive:   { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/automotive-pic.jpg", alt: "Mechanic in embroidered workwear next to a truck", name: "Automotive & Trades" },
    outdoor:      { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/outdoor-pic.jpg", alt: "Two hikers in outdoor apparel on a foggy trail", name: "Outdoor & Recreation" },
    golf:         { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/golf-pic.jpg", alt: "Two golfers walking the course in embroidered polos", name: "Golf & Country Club" },
    bags:         { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/bags-pic.jpg", alt: "Backpacks and duffel bags on display outdoors", name: "Bags & Accessories" },
    headwear:     { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/headwear-pic.jpg", alt: "Structured trucker cap product shot", name: "Headwear" },
    education:    { img: "https://threadifyapparel.com/wp-content/uploads/2026/07/threadify-pics2.jpg", alt: "Embroidery machine stitching a fraternity design", name: "Education & Youth" }
  };
  var indTabs = document.querySelectorAll(".ind-tab");
  var indPhotoImg = document.getElementById("indPhotoImg");
  var indPhotoPanel = document.getElementById("indPhotoPanel");
  var indPhotoTitle = document.getElementById("indPhotoTitle");
  var indPhotoGo = document.getElementById("indPhotoGo");
  function setIndustryTab(slug) {
    var p = INDUSTRY_PHOTOS[slug];
    if (!p) return;
    indTabs.forEach(function (t) {
      var on = t.getAttribute("data-industry") === slug;
      t.classList.toggle("active", on);
      t.setAttribute("aria-selected", on ? "true" : "false");
    });
    indPhotoImg.style.opacity = "0";
    setTimeout(function () {
      indPhotoImg.src = p.img;
      indPhotoImg.alt = p.alt;
      indPhotoImg.style.opacity = "1";
    }, 140);
    indPhotoTitle.textContent = p.name;
    indPhotoGo.textContent = "Browse " + p.name + " garments →";
    indPhotoPanel.href = "/order-builder/?industry=" + slug;
  }
  indTabs.forEach(function (t) {
    t.addEventListener("click", function () { setIndustryTab(t.getAttribute("data-industry")); });
  });

  var filterBtns = document.querySelectorAll(".filter-btn");
  var items = document.querySelectorAll(".work-item");
  var emptyState = document.getElementById("emptyState");
  function applyFilter(filter) {
    var visible = 0;
    items.forEach(function (item) {
      var match = filter === "all" || item.getAttribute("data-category") === filter;
      item.classList.toggle("hide", !match);
      if (match) visible++;
    });
    emptyState.classList.toggle("hide", visible > 0);
  }
  filterBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      filterBtns.forEach(function (b) { b.classList.remove("active"); });
      btn.classList.add("active");
      applyFilter(btn.getAttribute("data-filter"));
    });
  });
})();
</script>
<!--
  Cart badge/drawer module -- ported from the homepage, deliberately left INERT
  (type="text/plain" so the browser never executes it) per Diego\'s 2026-08-24
  call to defer cart UI until the new page layout is settled. To reactivate:
  remove the type="text/plain" attribute below. Do not delete this block.
-->
<script type="text/plain" id="tse-cart-module-disabled">
' . tse_cart_module_source() . '
</script>';
}
