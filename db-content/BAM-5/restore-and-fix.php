<?php
/**
 * BAM-5 follow-up — repair the backslashes fix-nav.php destroyed, and re-apply
 * the nav fix correctly.
 *
 * WHAT WENT WRONG
 * fix-nav.php passed raw content to wp_update_post(), which calls wp_unslash()
 * on its input (a magic-quotes legacy: WP expects *slashed* data). That silently
 * stripped one level of backslash escaping, three occurrences per page:
 *
 *   content:"\2713"   ->  content:"2713"    CSS escape for the checkmark glyph;
 *                                           bullets now render literal "2713"
 *   "\"": "&quot;"    ->  """: "&quot;"     JavaScript syntax error
 *   You\'ll continue  ->  You'll continue   JavaScript syntax error
 *
 * The two syntax errors kill the whole inline <script> block on both pages.
 *
 * THE FIX
 * Rebuild post_content from the pre-fix backup, re-insert the two nav links, and
 * write with wp_slash() so wp_update_post's wp_unslash() cancels out.
 *
 * Run on the server:
 *   DRY RUN:  wp eval-file /tmp/restore-and-fix.php
 *   APPLY:    BAM5_APPLY=1 wp eval-file /tmp/restore-and-fix.php
 *
 * Safe to re-run: it always rebuilds from the backup, so the result is identical
 * no matter how many times it runs or what state the post is currently in.
 */

$apply  = getenv( 'BAM5_APPLY' ) === '1';
$dir    = '/tmp/bam5-restore';
$pages  = [
    161966 => [ 'label' => 'Fundraisers', 'backup' => "$dir/161966.html" ],
    161967 => [ 'label' => 'Events',      'backup' => "$dir/161967.html" ],
];
$anchor = '<a href="/#work">Work</a>';
$insert = "\n        " . '<a href="/#about">About</a>'
        . "\n        " . '<a href="/#faq">FAQ</a>';

// The three escapes that must survive the write. If any is missing afterwards,
// the same bug has recurred and we must not report success.
$canaries = [
    'css-checkmark' => 'content:"\2713"',
    'js-quote-key'  => '"\"": "&quot;"',
    'js-apostrophe' => "You\\'ll continue",
];

echo $apply ? "MODE: APPLY\n\n" : "MODE: DRY RUN (no changes written)\n\n";

foreach ( $pages as $id => $meta ) {
    $label = $meta['label'];

    if ( ! is_readable( $meta['backup'] ) ) {
        echo "[$id $label] ERROR: backup not readable at {$meta['backup']} — SKIPPED\n\n";
        continue;
    }

    $orig = file_get_contents( $meta['backup'] );

    // The backup must be the pristine pre-fix content: all canaries present,
    // and the nav links not yet added.
    $missing = [];
    foreach ( $canaries as $name => $needle ) {
        if ( strpos( $orig, $needle ) === false ) { $missing[] = $name; }
    }
    if ( $missing ) {
        echo "[$id $label] ERROR: backup is not pristine, missing: "
             . implode( ', ', $missing ) . " — SKIPPED\n\n";
        continue;
    }
    if ( strpos( $orig, 'href="/#about"' ) !== false ) {
        echo "[$id $label] ERROR: backup already contains the nav links — SKIPPED\n\n";
        continue;
    }

    $hits = substr_count( $orig, $anchor );
    if ( $hits !== 1 ) {
        echo "[$id $label] ERROR: anchor matched {$hits}x (expected 1) — SKIPPED\n\n";
        continue;
    }

    $new = str_replace( $anchor, $anchor . $insert, $orig );

    $current = get_post( $id )->post_content;
    printf(
        "[%d %s] backup %d bytes -> target %d bytes (live is currently %d)\n",
        $id, $label, strlen( $orig ), strlen( $new ), strlen( $current )
    );

    if ( $current === $new ) {
        echo "[$id $label] already correct — nothing to do\n\n";
        continue;
    }

    if ( ! $apply ) {
        echo "[$id $label] WOULD REWRITE (restores 3 escapes, keeps the 2 nav links)\n\n";
        continue;
    }

    // wp_slash() compensates for the wp_unslash() inside wp_update_post().
    $res = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $new ) ], true );

    if ( is_wp_error( $res ) ) {
        echo "[$id $label] ERROR: " . $res->get_error_message() . "\n\n";
        continue;
    }

    // Read back and prove the bytes landed exactly as intended.
    clean_post_cache( $id );
    $after = get_post( $id )->post_content;

    $bad = [];
    foreach ( $canaries as $name => $needle ) {
        if ( strpos( $after, $needle ) === false ) { $bad[] = $name; }
    }

    if ( $after === $new && ! $bad ) {
        echo "[$id $label] UPDATED ok — round-trip verified, all 3 escapes intact\n\n";
    } else {
        echo "[$id $label] *** WRITE DID NOT ROUND-TRIP ***\n";
        echo "           expected " . strlen( $new ) . " bytes, got " . strlen( $after ) . "\n";
        echo "           missing escapes: " . ( $bad ? implode( ', ', $bad ) : 'none' ) . "\n\n";
    }
}

echo "Verify with:\n";
echo "  curl -s 'https://threadifyapparel.com/events/?cb=\$RANDOM' | grep -c '\"\\\\\"\": \"&quot;\"'\n";
echo "  (expect 1 — the escaped JS key restored; 0 means still broken)\n";
