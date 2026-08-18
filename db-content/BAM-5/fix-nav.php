<?php
/**
 * BAM-5 — restore missing "About" and "FAQ" header links on Fundraisers + Events.
 *
 * These pages are rendered by code-snippets snippet id=6 straight from
 * wp_posts.post_content, so this markup is NOT in the git repo and cannot be
 * shipped by the GitHub -> WP.com deployment. It has to be patched in the DB.
 *
 * Run on the server:
 *   DRY RUN:  wp eval-file /tmp/fix-nav.php
 *   APPLY:    wp eval-file /tmp/fix-nav.php --apply
 *
 * Safe to re-run: pages already carrying the links are skipped.
 */

$apply   = in_array( '--apply', $args ?? [], true ) || getenv( 'BAM5_APPLY' ) === '1';
$pages   = [ 161966 => 'Fundraisers', 161967 => 'Events' ];
$anchor  = '<a href="/#work">Work</a>';
$insert  = "\n        " . '<a href="/#about">About</a>'
         . "\n        " . '<a href="/#faq">FAQ</a>';
$backups = '/tmp/bam5-backups';

if ( ! is_dir( $backups ) ) { mkdir( $backups, 0700, true ); }

echo $apply ? "MODE: APPLY\n\n" : "MODE: DRY RUN (no changes written)\n\n";

foreach ( $pages as $id => $label ) {
    $post = get_post( $id );

    if ( ! $post ) {
        echo "[$id $label] ERROR: post not found — ABORTING\n";
        continue;
    }

    $content = $post->post_content;

    if ( strpos( $content, 'href="/#about"' ) !== false ) {
        echo "[$id $label] SKIP: About link already present\n";
        continue;
    }

    $hits = substr_count( $content, $anchor );
    if ( $hits !== 1 ) {
        echo "[$id $label] ERROR: anchor matched {$hits}x (expected exactly 1) — NOT MODIFIED\n";
        echo "           anchor: {$anchor}\n";
        continue;
    }

    $new = str_replace( $anchor, $anchor . $insert, $content );

    // Always write a backup of the original before touching anything.
    $file = "{$backups}/{$id}-" . date( 'Ymd-His' ) . '.html';
    file_put_contents( $file, $content );
    echo "[$id $label] backup -> {$file}\n";

    if ( ! $apply ) {
        echo "[$id $label] WOULD INSERT 2 links after the Work link ("
             . strlen( $content ) . " -> " . strlen( $new ) . " bytes)\n\n";
        continue;
    }

    $res = wp_update_post( [ 'ID' => $id, 'post_content' => $new ], true );

    if ( is_wp_error( $res ) ) {
        echo "[$id $label] ERROR: " . $res->get_error_message() . "\n\n";
    } else {
        echo "[$id $label] UPDATED ok\n\n";
    }
}

echo "Done. Verify with:\n";
echo "  curl -s 'https://threadifyapparel.com/events/?cb=\$RANDOM' | grep -A10 'nav class=\"links\"'\n";
echo "Note: WP.com batcaches HTML for 300s — wait ~5 min or use a cache-buster.\n";
