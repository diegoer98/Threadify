# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repo is

A **file-only mirror of `wp-content/`** for threadifyapparel.com, a WordPress site hosted on
WordPress.com. There is no WP core, no `wp-config.php`, no local dev server, and no
build/lint/test tooling anywhere in the tree. Files reach production through the
GitHub → WP.com deployment, which ships **files only**.

Issues are tracked in Linear — see **Issue tracking** at the end of this file.

**Branches:** `main` is the only branch WP.com's GitHub Deployment is wired to (manual trigger,
not on-push). `Beta` exists but is not connected to any deploy target — WP.com staging access
was never obtained for it — so it currently has no functional meaning beyond a name. Don't
assume merging to `Beta` is safer or gated differently than merging to `main`; right now
neither one auto-deploys anything, and DB changes (see below) bypass both entirely.

## The single most important gotcha: most of the live site is not in this repo

The five main pages (`/`, `/fundraisers/`, `/events/`, `/order-builder/`, `/product-details/`)
are **not** rendered by any theme or plugin file here. The `code-snippets` plugin holds an
active snippet (id=6) that hooks `template_redirect` and echoes a standalone HTML document
built from the raw `wp_posts.post_content` of each page. That markup lives in the database.

**Which pages are which** (verified 2026-08-17 against the live sitemap):

| Renderer | Count | Pages |
|---|---|---|
| `threadify-expansion` files — **in this repo, deploys by push** | 22 | `/about/`, `/faq/`, `/embroidery/*`, `/dtf-printing/*`, `/design-digitizing/`, `/custom-patches/`, `/service-areas/*` |
| DB `post_content` + snippet id=6 — **not in this repo** | 5 | `/`, `/fundraisers/`, `/events/`, `/order-builder/`, `/product-details/` |

Tell them apart by fetching the page: plugin-rendered pages load
`threadify-expansion/css/styles.css`; DB-rendered pages are self-contained documents with
a single inline `<style>` block and load nothing from `wp-content` except uploads.

Practical consequences:

- Grepping this repo for text you can see on the homepage (nav labels, hero copy, section
  headings) returns **nothing**. That is expected, not a search failure.
- Those fixes cannot be deployed by pushing files. They require a DB change applied with
  WP-CLI on the server.
- `wp-content/themes/threadify*/index.php` contains a full one-page site, but it is not what
  renders those URLs. Do not assume editing it changes the live pages.

## DB changes: the `db-content/` convention

`db-content/<ISSUE-KEY>/` is the reviewable record of a database change — the repo can't ship
it, so it documents it. Follow the shape of `db-content/BAM-5/`:

- `README.md` — why the change can't live in `wp-content/`, the bug, how to apply, how to
  verify, how to roll back, and any open questions.
- `before/*.html` — the live markup as captured, dated.
- `fix-*.php` — a WP-CLI `eval-file` script that is **dry-run by default** (`--apply` to
  write), idempotent (skips already-patched posts), backs the original `post_content` up to
  `/tmp/` before writing, and refuses to touch a post whose anchor text doesn't match exactly
  once.

Applying and verifying (see `db-content/BAM-5/README.md` for the full flow):

```sh
ssh wpcom "cat > /tmp/fix-nav.php" < db-content/BAM-5/fix-nav.php
ssh wpcom "wp eval-file /tmp/fix-nav.php"            # dry run
ssh wpcom "wp eval-file /tmp/fix-nav.php --apply"    # apply

curl -s "https://threadifyapparel.com/events/?cb=$RANDOM" | grep -A10 'nav class="links"'
```

WP.com batcaches HTML for 300s (look for `batcached for 300 seconds` in the page footer
comment) — always verify with a cache-buster or wait ~5 minutes.

Links inside subpage markup are root-relative (`/#services`, `/#about`), because bare
fragments would be dead anchors on pages that lack those sections.

## Custom code vs. vendored plugins

Only three things here are ours:

- `wp-content/plugins/threadify-expansion/` — the SEO service-page system.
- `wp-content/themes/threadify1|2|3/` — the custom one-page themes.
- `wp-content/uploads/` — site media, tracked in git.

Everything else under `wp-content/plugins/` is a third-party plugin vendored wholesale
(`wpforms-lite` alone is ~4700 of the repo's ~7400 tracked files, plus `woocommerce-shipping`,
`woocommerce-paypal-payments`, `code-snippets`, `tiktok-for-business`,
`ai-provider-for-anthropic`). Don't edit, review, or refactor those — including their bundled
`vendor/` and `docs/` directories, whose contents belong to their upstream projects.

`.gitignore` excludes WP.com-managed plugin symlinks (jetpack, woocommerce, gutenberg, …),
`wp-config.php`, SQL dumps, and `wc-logs/` (order/customer data).

## `threadify-expansion` architecture

A self-contained SEO site expansion that ignores the active theme.

- **Page generation is activation-time only.** `register_activation_hook` → `tse_activate()`
  runs `tse_create_all_pages()` (from `tse_page_definitions()` in `includes/create-pages.php`),
  then `tse_create_city_pages()` (needs the `/service-areas/` parent to exist first), then
  `tse_create_menu()`. Creation is guarded by `get_page_by_path()`, so **editing page copy in
  `includes/page-content.php` does not update pages that already exist** — existing pages only
  get their `_tse_*` meta refreshed. Changing live copy means reactivating the plugin against
  deleted pages, or a DB update.
- **Page identity is a meta flag.** `_tse_service_page = '1'` marks a plugin page;
  `tse_is_service_page()` gates every hook. `_tse_seo_title` and `_tse_meta_desc` drive
  `document_title_parts` and the `wp_head` meta/OG tags.
- **Rendering.** `template_include` (priority 99) swaps in `templates/service-page.php`, which
  emits a complete standalone dark/gold document — its own nav, dropdowns, breadcrumbs, and
  footer — so it looks nothing like the active theme. `css/styles.css` is enqueued only on
  service pages.
- **Content bodies are PHP functions** in `includes/page-content.php` returning HTML strings,
  composed from small helpers (`tse_cta_btn`, `tse_faq`, `tse_process`, `tse_icon_grid`,
  `tse_related`). A page definition references one by name via `content_fn`.
- **The homepage nav is patched from JavaScript.** `tse_inject_homepage_nav()` prints inline
  CSS/JS on `wp_footer` that finds the `<a href="#services">Services</a>` link in the DOM,
  removes it, and inserts the dropdown nav in its place. This DOM surgery exists precisely
  because the homepage markup comes from the DB and can't be edited in a template. The nav
  tree is duplicated in three places — the injected `NAV_ITEMS`, `templates/service-page.php`'s
  `$tse_nav`, and `includes/create-menu.php` — keep them in sync.

## Themes

Single-template themes: `index.php` renders the entire one-page site (nav, hero, services,
portfolio, testimonials, contact); `functions.php` enqueues Google Fonts + `style.css` and
returns the whole site's JavaScript from a heredoc (jQuery `$` is escaped as `\$` inside it) —
portfolio expand/collapse, mobile nav toggle, and a drag-and-drop contact form upload
(10 MB cap; jpg/png/gif/pdf/ai/eps/svg/zip) posted over `admin-ajax.php`.

`threadify2` and `threadify3` are **byte-identical except for the `Theme Name:` header** —
they are copies, so a change to one almost certainly belongs in the other. `threadify1` is an
older, smaller variant without the portfolio slideshow. Slideshow images live in each theme's
own `images/` and are referenced via `get_template_directory_uri()`.

## Image conventions

`wp-content/uploads/` is versioned, so image weight is a real concern. The optimization pass
in `8f53d11` set the working rules: photos capped at 1400px on the longest side, progressive
JPEG at quality 82; the social preview held at exactly 1200×630 so Open Graph cards keep
rendering; assets used as cropped CSS backgrounds downscaled proportionally.

## What is *not* in the database

Worth knowing before assuming a change needs DB surgery — most site data is already
file-based or external:

- **Product catalog** — `wp-content/uploads/2026/07/catalog-*.txt` (~8 MB of JSON behind a
  one-line header), fetched client-side by the order builder via `TDFY_DATA_BASE`. Tracked
  in git; edit it like any other file.
- **Form submissions** — both `#quoteForm`s POST to `api.web3forms.com`, falling back to a
  `mailto:orders@threadifyapparel.com` link. WordPress never receives them, so `wpforms-lite`
  is not in the request path.
- **E-commerce** — WooCommerce is installed but carries **zero products**
  (`/wp-json/wc/store/v1/products` returns `[]`). The "cart" on the homepage and order
  builder is hand-rolled JS state, not Woo. Don't reach for WooCommerce APIs to change it.

The DB holds page markup, snippet id=6, WP settings/menus, and one `hello-world` post.

## Working on database-backed pages

WP-CLI runs on the WP.com server, never locally. Add an alias to `~/.ssh/config`
(the keypair already exists at `~/.ssh/threadify_wpcom`; the username comes from the WP.com
dashboard under Hosting → SFTP/SSH):

```
Host wpcom
  HostName ssh.wp.com
  User <username-from-wpcom-dashboard>
  IdentityFile ~/.ssh/threadify_wpcom
```

Confirm with `ssh wpcom "wp option get home"`. Useful reads:

```sh
ssh wpcom "wp post get 161966 --field=post_content"   # a DB-rendered page's markup
ssh wpcom "wp post list --post_type=page --fields=ID,post_title,post_name"
```

### `wp_slash()` — the one that will bite you

**Always wrap content passed to `wp_update_post()` in `wp_slash()`.**

```php
wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $new ) ], true );
```

`wp_update_post()` runs `wp_unslash()` on its input (it expects already-slashed data, a
magic-quotes legacy). Passing raw content strips one level of backslash escaping
*everywhere in the document* — silently, with no error and a successful return value.

This corrupted both `/fundraisers/` and `/events/` on 2026-08-18: a CSS `\2713` checkmark
glyph became literal `2713`, and two JS escapes (`"\""` and `You\'ll`) became syntax errors
that killed the entire inline `<script>` block. See `db-content/BAM-5/README.md` for the
full incident. The same hazard applies to `wp post update <id> <file>`.

Rules for any DB change:

1. **Write it as a `db-content/<TICKET>/` script**, not as ad-hoc commands — dry-run by
   default, idempotent, backs up before writing, aborts on an ambiguous match.
2. **`wp_slash()` on write**, and **re-read the post afterwards inside the script** and
   assert the bytes round-tripped, so a bad write reports itself instead of looking like
   success.
3. **Verify with a full `diff` against the backup**, never a spot-check for the change you
   intended. The corruption above was invisible to "are the two links present?" — they were.
   A byte count that doesn't match the dry run's prediction is a real signal; chase it.
4. **Commit the pre-change backup** into `db-content/<TICKET>/backups/`. `/tmp` on the
   server is ephemeral and is not a rollback path.
5. **Post the verification output to the Linear ticket afterwards.** A merged PR containing
   only `db-content/` proves the fix was *written*, not that it is *live* — the Linear
   comment is the only deploy record a DB change ever gets.

### The gate: PR approval before `--apply`, no exceptions

BAM-5 went live on 2026-08-18 by running `--apply` straight over SSH, before the
`db-content/BAM-5/` PR had a single review on it — the PR was opened *after* the fact, as a
record, not a gate. That surprised the site owner and is not how this works going forward.

**The `db-content/<TICKET>/` PR must be opened and have an approving review *before* anyone
runs the real apply command** (`--apply` / `<TICKET>_APPLY=1`) against production. The dry
run is safe to run anytime — it writes nothing — and its output belongs in the PR for the
reviewer to check against the diff. Only after merge does the apply step run. There is no
"I'll just apply it now and open the PR after" — that is the exact failure this rule exists
to close off.

## Issue tracking

Tickets belong to the **THR** team, `https://linear.app/threadifyapparel/team/THR`, where the
other devs are. Do not file into the personal BamBamJr workspace (`BAM-*`); issue BAM-5
predates this convention. Branches follow Linear's `gitBranchName`.
