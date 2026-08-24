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
ssh wpcom "cat > /tmp/fix-nav.php" < db-content/BAM-5/fix-nav.php

# dry run first — prints what it would change, writes nothing
ssh wpcom "wp eval-file /tmp/fix-nav.php"

# apply — NOTE: `--apply` does not survive `wp eval-file` (WP-CLI rejects
# unknown flags first), so the env var is the real switch
ssh wpcom "BAM5_APPLY=1 wp eval-file /tmp/fix-nav.php"
```

## Incident — backslash corruption, 2026-08-18

The first apply of `fix-nav.php` inserted the two links **and silently corrupted
three backslash escapes per page.** Both pages' inline JavaScript was left with
syntax errors for roughly four minutes before it was caught and reverted.

`wp_update_post()` runs `wp_unslash()` on its input — a magic-quotes legacy: it
expects data that has already been slashed. The script passed raw
`post_content`, so one level of escaping was stripped:

| Before | After | Effect |
|---|---|---|
| `content:"\2713"` | `content:"2713"` | CSS checkmark glyph became literal text `2713` on every bullet |
| `"\"": "&quot;"` | `""": "&quot;"` | JavaScript syntax error |
| `You\'ll continue` | `You'll continue` | JavaScript syntax error |

**How it was caught.** The dry run predicted `31710 -> 31778` bytes (+68, the two
links). The applied result was 31775 — three bytes short, one per stripped
backslash. Chasing that 3-byte gap with a full `diff` against the backup exposed
it. Verifying only "are the two links present?" would have missed it entirely,
because the links *were* correctly present.

**Fix.** `restore-and-fix.php` rebuilds `post_content` from the pre-fix backup,
re-inserts the links, and writes with `wp_slash()`. It re-reads the post
afterwards and asserts the bytes round-tripped exactly, refusing to report
success otherwise. `fix-nav.php` has been corrected the same way.

**Rules this establishes for every future DB change:**

1. **Always `wp_slash()` content passed to `wp_update_post()`.**
2. **Verify with a full `diff` against the backup, never a spot-check** for the
   change you intended. Byte-count deltas that don't match the prediction are a
   signal, not a rounding error.
3. **Round-trip inside the script** — re-read after writing and compare, so a
   bad write reports itself instead of looking like success.

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

The pristine pre-fix `post_content` for both pages is committed here in
`backups/` — `/tmp/bam5-backups/` on the server is ephemeral and must not be
relied on.

```sh
# restore the exact pre-fix state (drops the two nav links as well)
ssh wpcom "mkdir -p /tmp/bam5-restore"
for id in 161966 161967; do
  ssh wpcom "cat > /tmp/bam5-restore/$id.html" < db-content/BAM-5/backups/$id-20260818-040921.html
done
ssh wpcom "wp post update 161966 /tmp/bam5-restore/161966.html"
```

⚠️ `wp post update <id> <file>` has the **same unslashing hazard** described in
the incident above. Verify with a full `diff` afterwards, or restore via
`restore-and-fix.php`, which handles slashing and self-checks the round trip.

## Open question

The homepage's `FAQ` link targets the `#faq` anchor on the homepage, but a
standalone `/faq/` page also exists. This patch uses `/#faq` for consistency.
If `/faq/` is the intended destination, change it here **and** on the homepage
so all three agree.
