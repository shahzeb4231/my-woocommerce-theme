<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** Contact form handler (works for logged-in and guest visitors) */
function xsn_handle_contact() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( 'contact', $back );
	$go   = function ( $status ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'contact', $status, $back ) . '#contact' );
		exit;
	};

	$nonce = isset( $_POST['xsn_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['xsn_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'xsn_contact' ) ) $go( 'error' );

	// Honeypot: bots fill this hidden field, humans never see it
	if ( ! empty( $_POST['website'] ) ) $go( 'sent' );

	// Max 5 messages per hour from one visitor
	$ip    = function_exists( 'xsn_client_ip' ) ? xsn_client_ip() : 'unknown';
	$key   = 'xsn_ct_' . md5( $ip );
	$tries = (int) get_transient( $key );
	if ( $tries >= 5 ) $go( 'error' );
	set_transient( $key, $tries + 1, HOUR_IN_SECONDS );

	$name    = isset( $_POST['name'] )    ? sanitize_text_field( wp_unslash( $_POST['name'] ) )        : '';
	$email   = isset( $_POST['email'] )   ? sanitize_email( wp_unslash( $_POST['email'] ) )            : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $message ) $go( 'invalid' );
	if ( strlen( $message ) > 4000 ) $go( 'invalid' );

	$body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}\n";
	$sent = wp_mail(
		get_option( 'admin_email' ),
		'New message from the XASSIDA SN website',
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	$go( $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_xsn_contact', 'xsn_handle_contact' );
add_action( 'admin_post_xsn_contact', 'xsn_handle_contact' );
