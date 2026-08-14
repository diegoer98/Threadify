<?php
/**
 * Threadify – functions.php
 * Enqueues Google Fonts, registers scripts, handles contact form AJAX with file upload.
 */

/* ─── Enqueue styles & scripts ──────────────────────────── */
add_action( 'wp_enqueue_scripts', 'threadify_assets' );
function threadify_assets() {
    // Google Fonts
    wp_enqueue_style(
        'threadify-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=DM+Sans:wght@400;600;700&display=swap',
        [],
        null
    );

    // Main stylesheet
    wp_enqueue_style( 'threadify-style', get_stylesheet_uri(), ['threadify-fonts'], '1.0' );

}

/* ─── Inline JavaScript ─────────────────────────────────── */
function threadify_inline_js() {
    $ajax = esc_url( admin_url( 'admin-ajax.php' ) );
    return <<<JS
(function($){
    $(function(){

        /* ── Portfolio expand/collapse ── */
        $('.portfolio-item').on('click', function(){
            var $item = $(this);
            var wasOpen = $item.hasClass('open');
            // close all
            $('.portfolio-item').removeClass('open');
            // toggle clicked
            if (!wasOpen) $item.addClass('open');
        });

        /* ── Mobile nav toggle ── */
        var \$toggle = \$('#nav-toggle');
        var \$links  = \$('#nav-links');
        \$toggle.on('click', function(){
            var open = \$links.hasClass('open');
            \$links.toggleClass('open', !open);
            \$toggle.attr('aria-expanded', String(!open));
        });
        // Close on nav link click
        \$links.find('a').on('click', function(){ \$links.removeClass('open'); \$toggle.attr('aria-expanded','false'); });

        /* ── Upload field ── */
        var \$zone    = \$('#tf-drop-zone');
        var \$fileIn  = \$('#tf-file');
        var \$preview = \$('#tf-preview');
        var \$fname   = \$('#tf-file-name');
        var \$err     = \$('#tf-upload-error');
        var \$remove  = \$('#tf-remove');
        var MAX      = 10 * 1024 * 1024;
        var ALLOWED  = /\\.(jpe?g|png|gif|pdf|ai|eps|svg|zip)$/i;

        function showFile(file){
            \$err.text('').removeClass('visible');
            if (!file) return;
            if (!ALLOWED.test(file.name)) {
                \$err.text('File type not allowed. Use JPG, PNG, PDF, AI, EPS, SVG, or ZIP.').addClass('visible');
                \$fileIn.val(''); return;
            }
            if (file.size > MAX) {
                \$err.text('File is too large (max 10 MB).').addClass('visible');
                \$fileIn.val(''); return;
            }
            \$fname.text(file.name);
            \$preview.addClass('visible');
            \$zone.find('.upload-label-text').html('<span>File selected</span>');
        }

        \$zone.on('dragover dragenter', function(e){ e.preventDefault(); \$(this).addClass('dragover'); })
             .on('dragleave drop',      function(){  \$(this).removeClass('dragover'); });
        \$fileIn.on('change', function(){ showFile(this.files[0]); });
        \$remove.on('click', function(){
            \$fileIn.val('');
            \$preview.removeClass('visible');
            \$zone.find('.upload-label-text').html('<span>Click to browse</span> or drag &amp; drop');
            \$err.text('').removeClass('visible');
        });

        /* ── Contact form submit ── */
        \$('#threadify-contact-form').on('submit', function(e){
            e.preventDefault();
            var \$form = \$(this);
            var \$btn  = \$('#tf-submit');

            var name    = \$('#tf-name').val().trim();
            var email   = \$('#tf-email').val().trim();
            var message = \$('#tf-message').val().trim();

            if (!name || !email || !message) {
                alert('Please fill in all required fields.');
                return;
            }

            \$btn.prop('disabled', true).text('Sending…');

            var fd = new FormData(\$form[0]);
            fd.append('action', 'threadify_contact');
            fd.append('nonce', \$('#threadify_nonce').val());

            \$.ajax({
                url: '$ajax',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(resp){
                    if (resp.success) {
                        \$btn.text('Sent ✓');
                        \$form[0].reset();
                        \$preview.removeClass('visible');
                        \$zone.find('.upload-label-text').html('<span>Click to browse</span> or drag &amp; drop');
                        \$('#tf-success').addClass('visible');
                    } else {
                        \$btn.prop('disabled', false).text('Send Message');
                        alert(resp.data || 'Something went wrong. Please try again.');
                    }
                },
                error: function(){
                    \$btn.prop('disabled', false).text('Send Message');
                    alert('Network error – please try again.');
                }
            });
        });
    });
})(jQuery);
JS;
}

/* ─── AJAX: handle contact form ─────────────────────────── */
add_action( 'wp_ajax_threadify_contact',        'threadify_handle_contact' );
add_action( 'wp_ajax_nopriv_threadify_contact', 'threadify_handle_contact' );

function threadify_handle_contact() {

    if ( ! isset( $_POST['nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'threadify_contact_nonce' ) ) {
        wp_send_json_error( 'Security check failed.' );
    }

    $name    = sanitize_text_field(    wp_unslash( $_POST['name']    ?? '' ) );
    $email   = sanitize_email(         wp_unslash( $_POST['email']   ?? '' ) );
    $message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

    if ( ! $name || ! $email || ! $message ) {
        wp_send_json_error( 'Please fill in all required fields.' );
    }
    if ( ! is_email( $email ) ) {
        wp_send_json_error( 'Please enter a valid email address.' );
    }

    $to      = 'Orders@ThreadifyApparel.com';
    $subject = 'New Enquiry from ' . $name . ' – Threadify';
    $body    = "Name: {$name}\r\nEmail: {$email}\r\n\r\nMessage:\r\n{$message}\r\n";
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    $attachment_path = '';
    if ( ! empty( $_FILES['threadify_attachment']['tmp_name'] ) ) {
        $file     = $_FILES['threadify_attachment'];
        $allowed  = [ 'jpg','jpeg','png','gif','pdf','ai','eps','svg','zip' ];
        $ext      = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

        if ( $file['size'] > 10 * 1024 * 1024 ) {
            wp_send_json_error( 'Attached file exceeds the 10 MB limit.' );
        }
        if ( ! in_array( $ext, $allowed, true ) ) {
            wp_send_json_error( 'File type not allowed.' );
        }

        $upload_dir = wp_upload_dir();
        $dest       = trailingslashit( $upload_dir['basedir'] ) . 'threadify-temp/' . time() . '-' . sanitize_file_name( $file['name'] );
        wp_mkdir_p( dirname( $dest ) );

        if ( move_uploaded_file( $file['tmp_name'], $dest ) ) {
            $attachment_path = $dest;
            $body .= "\r\nAttached: " . basename( $dest );
        }
    }

    $attachments = $attachment_path ? [ $attachment_path ] : [];
    $sent        = wp_mail( $to, $subject, $body, $headers, $attachments );

    if ( $attachment_path && file_exists( $attachment_path ) ) {
        wp_delete_file( $attachment_path );
    }

    $sent ? wp_send_json_success() : wp_send_json_error( 'Could not send email. Please try again.' );
}

/* ─── Add theme support basics ──────────────────────────── */
add_action( 'after_setup_theme', function() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'html5', ['search-form','comment-form','gallery','caption'] );
});
