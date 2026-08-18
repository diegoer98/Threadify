# Stripe checkout + order options — setup guide

Covers the self-serve flow: customer picks a product, selects a placement, uploads
artwork, and pays by card at checkout, with both the placement and the file visible
on the order in WooCommerce admin.

B2B deposit work is deliberately **not** covered here — that stays on Stripe
Invoicing from the Stripe Dashboard, with no code involved.

---

## 1. What is in this repository

One new plugin, `wp-content/plugins/threadify-order-options/`:

| File | Role |
| --- | --- |
| `threadify-order-options.php` | Bootstrap, constants, placement list |
| `includes/uploads.php` | Artwork validation, storage, secure download |
| `includes/product-fields.php` | Per-product toggle, storefront fields |
| `includes/cart-order.php` | Cart → order → admin plumbing, cleanup cron |
| `css/order-options.css` | Field styling |

Nothing else in the repo changed. **The Stripe gateway is not in this repository**
and cannot be — see the next section.

---

## 2. Why the Stripe plugin is not committed here

This repo holds `wp-content` only. There is no WordPress core, no database, and
no `wp-config.php`. The `.gitignore` also excludes `wp-content/plugins/woocommerce`
and `wp-content/plugins/woocommerce-payments` because they are WordPress.com-managed
symlinks.

Payment gateways are installed through WP admin and configured into the database.
Committing plugin files would not install anything, and API keys must never be
committed. Everything in sections 3–6 is done in a browser.

---

## 3. Which gateway to install — and why not WooPayments

**Install: WooCommerce Stripe Payment Gateway** (slug `woocommerce-gateway-stripe`).
Official WooCommerce, v10.8.5, last updated 5 Aug 2026, 700,000+ installs.

**Do not use WooPayments for this**, even though it is already available on your
WordPress.com plan. WooPayments creates a Stripe *Express* account and
[never exposes the API keys, even after setup is complete][woo-compare]. That means:

- your existing sandbox account `acct_1U5ajC88kohRarIo` would sit unused;
- your B2B Stripe Invoicing would live on a **different Stripe account** from your
  checkout revenue — split payouts, split reporting, two reconciliations every month.

The Stripe plugin uses *your* account and *your* keys, so checkout and invoicing
stay on one ledger.

If WooPayments is currently enabled, disable it (**WooCommerce → Settings → Payments**)
so customers are not offered two card gateways.

[woo-compare]: https://woocommerce.com/document/woopayments/compatibility/woopayments-vs-stripe-plugin-comparison/

### Install steps

1. **Plugins → Add New**, search "WooCommerce Stripe Payment Gateway", install, activate.
   (WordPress.com requires a Business or Commerce plan for plugin installs.)
2. **Plugins**, activate **Threadify Site Expansion** if not already, and activate
   **Threadify Order Options** once this branch is deployed.

---

## 4. Stripe Dashboard — do this first

Work in your **sandbox**, not live. Confirm the account switcher shows the sandbox
tied to `acct_1U5ajC88kohRarIo`.

1. **Developers → API keys.** Copy the **Publishable key** (`pk_test_…`) and
   **Secret key** (`sk_test_…`). If the keys start `pk_live_` / `sk_live_`, you are
   in the wrong mode — switch to test/sandbox.
2. **Settings → Payment methods.** Enable cards. Enable Apple Pay / Google Pay / Link
   only if you want express buttons at checkout.
3. Leave the webhook for step 5 — you need a URL from the plugin first.

**Nothing here goes into the repository.** Keys are entered in WP admin and stored
in the database.

---

## 5. WooCommerce → Settings → Payments → Stripe

1. **Enable test mode** — tick it before entering anything else.
2. Paste the **test publishable key** and **test secret key**.
3. The settings screen displays the exact **webhook endpoint URL** for your site.
   Copy it, then in Stripe go to **Developers → Webhooks → Add endpoint**, paste it,
   and select the events the plugin lists on that same settings screen.
4. Stripe shows a **signing secret** (`whsec_…`) after the endpoint is created.
   Paste it back into the plugin's webhook secret field.
5. Save.

Webhooks are what mark orders paid when a customer closes the tab mid-redirect. Skip
this and you will get stuck "pending payment" orders.

Also confirm: **WooCommerce → Settings → General** has currency USD and the store
address set to Federal Way, WA (drives tax and Stripe's address checks).

---

## 6. Turn on the order options per product

The placement + upload fields are **opt-in per product**, so blanks, gift cards and
digitizing services are not forced to demand artwork.

For each product that needs decoration:

1. **Products → edit the product → Product data → General.**
2. Tick **Decoration options**.
3. Update.

On those products the storefront then shows a required **Placement** dropdown
(Chest / Sleeve / Back) and a required **design file** upload. On shop and category
listings the add-to-cart button becomes **Select options** and links to the product
page — the AJAX loop button cannot carry a file, so allowing it would let an order
reach checkout with no placement and no artwork.

To change the placement list without editing the plugin, filter `tfo_placements`.

---

## 7. End-to-end test

Run this in test mode before switching anything live.

- [ ] Product page shows Placement and design file fields
- [ ] Add to cart with **no** placement → blocked with a notice
- [ ] Add to cart with **no** file → blocked with a notice
- [ ] Upload a `.php` renamed to `.png` → blocked
- [ ] Upload a valid PNG or PDF with a placement → added to cart
- [ ] Cart and checkout both show Placement and the design filename
- [ ] Pay with test card `4242 4242 4242 4242`, any future expiry, any CVC, any ZIP
- [ ] Order reaches **Processing** (not stuck on Pending — if stuck, recheck webhooks)
- [ ] **WooCommerce → Orders → the order** shows Placement on the line item
- [ ] The **Download** button on that line item returns the original file
- [ ] Log out, paste that download URL → denied
- [ ] Confirmation email shows Placement and the design filename

Add two of the same product with different placements/files and confirm they stay
as **separate line items**.

---

## 8. Going live

Only after section 7 passes:

1. Stripe Dashboard → switch to **live** mode, copy the live keys.
2. Stripe → **Developers → Webhooks** → add the same endpoint URL again in live mode,
   copy the new signing secret.
3. WooCommerce → Stripe settings → **untick test mode**, paste live keys + live
   signing secret, save.
4. Place one real low-value order with a real card and refund it.

Test and live keys are separate, and **so are webhook endpoints** — a live endpoint
is not created for you when you flip the toggle.

---

## 9. B2B deposits — unchanged, no code

Quote-and-deposit work stays manual, on Stripe Invoicing in the Dashboard:
**Invoicing → Create invoice**, set the deposit line (30–50%), send. Because checkout
runs on the same Stripe account, deposits and self-serve orders land in one payout
stream and one set of reports.

Nothing in this repo touches that flow.

---

## 10. Things worth knowing

**Upload size.** The plugin's ceiling is 25 MB but it clamps to whatever the host
allows (`wp_max_upload_size()`). WordPress.com's PHP limit may be lower, and the field
hint shows the real effective limit. If customers hit it, the honest fix is asking
them to email large artwork rather than raising limits.

**Where artwork lives.** `wp-content/uploads/threadify-order-files/`, under generated
names, never linked directly. Downloads run through a capability-checked handler
that forces a generic attachment, so a hostile SVG cannot execute against your admin
session. The bundled `.htaccess` only bites on Apache — WordPress.com runs nginx, so
the extension whitelist, not that file, is what keeps executable uploads off disk.

**Artwork is customer data.** Downloads are logged to the WooCommerce log
(`threadify-order-options` source). `wc-logs/` is already gitignored — keep it that
way.

**Abandoned carts.** A daily cron deletes artwork older than 90 days that no order
references. Files on real orders are never touched.

**Deleting an order does not delete its artwork.** Files are removed only by the
90-day orphan sweep once nothing references them.
