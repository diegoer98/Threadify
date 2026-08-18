# BAM-5 — missing "About" / "FAQ" header links

<https://linear.app/bambamjr/issue/BAM-5>

## Why this lives here and not in `wp-content/`

The 5 main pages (`/`, `/fundraisers/`, `/events/`, `/order-builder/`,
`/product-details/`) are **not** rendered from theme or plugin files. The
`code-snippets` plugin holds active snippet **id=6**, which hooks
`template_redirect` and echoes a standalone HTML document built from the raw
`wp_posts.post_content` of each page.

So the broken markup is in the **database**, not in this repo — grepping for
`"Plan a Fundraiser"` or `"Book Us"` returns nothing. The GitHub -> WP.com
deployment ships files only and **cannot** deliver this fix.

This directory is the reviewable record of a DB change: the before-state, the
patch, and the verification steps.

## The bug

`/events/` and `/fundraisers/` are missing two nav links the homepage has:

```html
<a href="/#about">About</a>
<a href="/#faq">FAQ</a>
```

See `before/` for the three nav blocks as captured from the live site
on 2026-08-17.

Links on subpages are root-relative (`/#services`), so the inserted links use
`/#about` and `/#faq`. Bare `#about` would be a dead anchor — those sections
don't exist on these pages.

## Applying

`fix-nav.php` patches pages 161966 (Fundraisers) and 161967 (Events). It is
idempotent, backs up the original `post_content` to `/tmp/bam5-backups/` before
writing, and refuses to touch a page whose anchor doesn't match exactly once.

```sh
# copy to the server
ssh <wpcom-user>@ssh.wp.com "cat > /tmp/fix-nav.php" < db-content/BAM-5/fix-nav.php

# dry run first — prints what it would change, writes nothing
ssh <wpcom-user>@ssh.wp.com "wp eval-file /tmp/fix-nav.php"

# apply
ssh <wpcom-user>@ssh.wp.com "wp eval-file /tmp/fix-nav.php --apply"
```

## Verifying

```sh
for p in "" events fundraisers; do
  echo "== /$p"
  curl -s "https://threadifyapparel.com/$p?cb=$RANDOM" | grep -A10 'nav class="links"'
done
```

All three should show the same 8 links. WP.com batcaches HTML for 300s
(`batcached for 300 seconds` in the page footer comment), so use the
cache-buster or wait ~5 minutes.

## Rolling back

```sh
ssh <wpcom-user>@ssh.wp.com "ls /tmp/bam5-backups/"
ssh <wpcom-user>@ssh.wp.com "wp post update 161967 /tmp/bam5-backups/161967-<stamp>.html"
```

## Open question

The homepage's `FAQ` link targets the `#faq` anchor on the homepage, but a
standalone `/faq/` page also exists. This patch uses `/#faq` for consistency.
If `/faq/` is the intended destination, change it here **and** on the homepage
so all three agree.
