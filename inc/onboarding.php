<?php
/**
 * Customer + vendor registration, vendor identity verification (KYC), admin review.
 * Vendors are created as Dokan sellers with selling DISABLED until an admin approves.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'WooCommerce' ) ) return;

/* ---------------------------------------------------------------
 * Helpers
 * ------------------------------------------------------------- */
function xsn_image_mimes() {
	return array( 'jpg|jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp' );
}

/** Private folder for ID documents (random file names, direct web access denied where .htaccess is honoured) */
function xsn_private_dir() {
	$u   = wp_upload_dir();
	$dir = trailingslashit( $u['basedir'] ) . 'xsn-private';
	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
		file_put_contents( $dir . '/.htaccess', "Options -Indexes\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" );
		file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" );
	}
	return $dir;
}

function xsn_fail( $msg, $field = '' ) {
	wp_send_json_error( array( 'message' => $msg, 'field' => $field ) );
}

/** Atomic lock (one request at a time per email / shop name). Returns a lock id or false. */
function xsn_lock( $name, $ttl = 60 ) {
	global $wpdb;
	$opt = 'xsn_lock_' . md5( $name );
	$sql = "INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no')";
	if ( $wpdb->query( $wpdb->prepare( $sql, $opt, (string) ( time() + $ttl ) ) ) ) return $opt;
	$exp = (int) $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $opt ) );
	if ( $exp && $exp < time() ) { // stale lock left by a crashed request
		$wpdb->delete( $wpdb->options, array( 'option_name' => $opt ) );
		if ( $wpdb->query( $wpdb->prepare( $sql, $opt, (string) ( time() + $ttl ) ) ) ) return $opt;
	}
	return false;
}
function xsn_unlock( $opt ) {
	global $wpdb;
	if ( $opt ) $wpdb->delete( $wpdb->options, array( 'option_name' => $opt ) );
}

function xsn_check_image( $key, $required ) {
	if ( empty( $_FILES[ $key ]['name'] ) ) {
		return $required ? new WP_Error( $key, 'Please upload the required photo.' ) : true;
	}
	$f = $_FILES[ $key ]; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! empty( $f['error'] ) ) return new WP_Error( $key, 'The upload failed. Please try again with a smaller image.' );
	if ( $f['size'] > 5 * MB_IN_BYTES ) return new WP_Error( $key, 'Each image must be smaller than 5 MB.' );
	$c = wp_check_filetype_and_ext( $f['tmp_name'], $f['name'], xsn_image_mimes() );
	if ( empty( $c['ext'] ) || empty( $c['type'] ) || ! @getimagesize( $f['tmp_name'] ) ) {
		return new WP_Error( $key, 'Please upload a JPG, PNG or WebP image.' );
	}
	return true;
}

/** Save an ID document into the private folder. Returns "uid/filename" or ''. */
function xsn_save_private_file( $key, $uid ) {
	if ( empty( $_FILES[ $key ]['name'] ) ) return '';
	$f = $_FILES[ $key ]; // phpcs:ignore WordPress.Security.NonceVerification
	$c = wp_check_filetype_and_ext( $f['tmp_name'], $f['name'], xsn_image_mimes() );
	if ( empty( $c['ext'] ) ) return '';
	$dir = xsn_private_dir() . '/' . (int) $uid;
	wp_mkdir_p( $dir );
	$name = bin2hex( random_bytes( 20 ) ) . '.' . $c['ext'];
	if ( ! move_uploaded_file( $f['tmp_name'], $dir . '/' . $name ) ) return '';
	return (int) $uid . '/' . $name;
}

/** Save a (public) profile photo to the media library. Returns attachment ID or 0. */
function xsn_save_profile_photo( $key, $uid ) {
	if ( empty( $_FILES[ $key ]['name'] ) ) return 0;
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	$id = media_handle_upload( $key, 0, array( 'post_author' => (int) $uid, 'post_title' => 'Profile photo ' . (int) $uid ) );
	return is_wp_error( $id ) ? 0 : (int) $id;
}

function xsn_mail( $to, $subject, $heading, $html ) {
	$body = '<div style="font-family:Arial,sans-serif;background:#f7f4ec;padding:24px"><div style="max-width:560px;margin:auto;background:#fff;border-radius:16px;overflow:hidden">'
		. '<div style="background:#0b3d2e;color:#c9a24b;padding:20px 28px;font-size:20px;font-weight:bold">XASSIDA SN</div>'
		. '<div style="padding:28px;color:#10211b;line-height:1.6"><h2 style="margin-top:0;color:#0b3d2e">' . esc_html( $heading ) . '</h2>' . $html . '</div></div></div>';
	return wp_mail( $to, $subject, $body, array( 'Content-Type: text/html; charset=UTF-8' ) );
}

function xsn_pending_count() {
	return count( get_users( array( 'meta_key' => 'xsn_kyc_status', 'meta_value' => 'pending', 'fields' => 'ID' ) ) );
}

/* ---------------------------------------------------------------
 * Registration (AJAX)
 * ------------------------------------------------------------- */
add_action( 'wp_ajax_nopriv_xsn_register', 'xsn_ajax_register' );
add_action( 'wp_ajax_xsn_register', 'xsn_ajax_register' );

/** Safety net: any unexpected error becomes a clear message (and a line in the PHP error log) instead of a silent failure */
function xsn_ajax_register() {
	try {
		xsn_register_process();
	} catch ( \Throwable $e ) {
		error_log( '[XSN register] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() );
		$d = array( 'message' => 'Sorry, something went wrong on our side. Please try again in a moment.', 'field' => '' );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$d['debug'] = $e->getMessage() . ' (' . basename( $e->getFile() ) . ':' . $e->getLine() . ')';
		}
		wp_send_json_error( $d );
	}
}

function xsn_register_process() {
	// PHP drops the whole form when the upload is bigger than post_max_size
	if ( empty( $_POST ) && ! empty( $_SERVER['CONTENT_LENGTH'] ) ) {
		xsn_fail( 'Your photos are too large to upload. Please choose smaller photos and try again.' );
	}

	if ( is_user_logged_in() ) xsn_fail( 'You are already signed in.' );

	$nonce = isset( $_POST['xsn_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['xsn_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'xsn_register' ) ) xsn_fail( 'Your session expired. Please refresh the page and try again.' );

	// Honeypot: bots fill this hidden field
	if ( ! empty( $_POST['website'] ) ) wp_send_json_success( array( 'redirect' => home_url( '/' ) ) );

	// Simple rate limit per IP
	$ip    = function_exists( 'xsn_client_ip' ) ? xsn_client_ip() : 'unknown';
	$key   = 'xsn_reg_' . md5( $ip );
	$tries = (int) get_transient( $key );
	if ( $tries >= 10 ) xsn_fail( 'Too many attempts. Please try again in an hour.' );
	set_transient( $key, $tries + 1, HOUR_IN_SECONDS );

	$vendor = isset( $_POST['type'] ) && 'vendor' === $_POST['type'];
	$g      = function ( $k ) {
		return isset( $_POST[ $k ] ) ? trim( sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	};

	$first   = $g( 'first_name' );
	$last    = $g( 'last_name' );
	$email   = sanitize_email( $g( 'email' ) );
	$pass    = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore
	$phone   = $g( 'phone' );
	$addr    = $g( 'address' );
	$city    = $g( 'city' );
	$zip     = $g( 'postcode' );
	$country = strtoupper( $g( 'country' ) );

	if ( '' === $first ) xsn_fail( 'Please enter your first name.', 'first_name' );
	if ( '' === $last ) xsn_fail( 'Please enter your last name.', 'last_name' );
	if ( ! is_email( $email ) ) xsn_fail( 'Please enter a valid email address.', 'email' );

	// Same request sent again (double click, timeout then retry, page refresh): return the account that was already created
	$token = isset( $_POST['xsn_token'] ) ? preg_replace( '/[^a-zA-Z0-9\-]/', '', (string) wp_unslash( $_POST['xsn_token'] ) ) : ''; // phpcs:ignore
	if ( strlen( $token ) >= 16 ) {
		$done = get_transient( 'xsn_idem_' . $token );
		if ( is_array( $done ) && ! empty( $done['uid'] ) && hash_equals( (string) $done['eh'], md5( strtolower( $email ) ) ) ) {
			wp_set_current_user( (int) $done['uid'] );
			wc_set_customer_auth_cookie( (int) $done['uid'] );
			wp_send_json_success( array( 'redirect' => wc_get_page_permalink( 'myaccount' ), 'repeat' => true ) );
		}
	}

	if ( email_exists( $email ) ) xsn_fail( 'An account with this email already exists. Please sign in instead.', 'email' );
	if ( strlen( $pass ) < 8 ) xsn_fail( 'Your password must be at least 8 characters.', 'password' );
	if ( function_exists( 'xsn_weak_password' ) && xsn_weak_password( $pass, $email ) ) xsn_fail( 'That password is too easy to guess. Please choose a stronger one.', 'password' );
	if ( ! preg_match( '/^\+?[0-9\s\-().]{7,20}$/', $phone ) ) xsn_fail( 'Please enter a valid phone number.', 'phone' );
	if ( strlen( $addr ) < 4 ) xsn_fail( 'Please enter your address.', 'address' );
	if ( '' === $city ) xsn_fail( 'Please enter your city.', 'city' );
	if ( ! isset( WC()->countries->get_countries()[ $country ] ) ) xsn_fail( 'Please choose your country.', 'country' );

	$shop = $id_type = $id_number = $id_expiry = '';
	if ( $vendor ) {
		if ( ! get_role( 'seller' ) ) xsn_fail( 'Vendor registration is not available right now.' );
		$shop      = $g( 'shop_name' );
		$id_type   = $g( 'id_type' );
		$id_number = $g( 'id_number' );
		$id_expiry = $g( 'id_expiry' );
		if ( strlen( $shop ) < 3 ) xsn_fail( 'Please enter your shop name (at least 3 characters).', 'shop_name' );
		if ( ! in_array( $id_type, array( 'National ID card', 'Passport', "Driver's licence" ), true ) ) xsn_fail( 'Please choose your ID type.', 'id_type' );
		if ( ! preg_match( '/^[A-Za-z0-9\s\-\/]{5,30}$/', $id_number ) ) xsn_fail( 'Please enter a valid ID number.', 'id_number' );
		if ( empty( $_POST['terms'] ) ) xsn_fail( 'Please accept the seller terms to continue.', 'terms' );
	}

	// Validate every upload BEFORE creating the account
	$files = $vendor ? array( 'photo' => true, 'id_front' => true, 'id_back' => false ) : array( 'photo' => false );
	foreach ( $files as $fkey => $required ) {
		$ok = xsn_check_image( $fkey, $required );
		if ( is_wp_error( $ok ) ) xsn_fail( $ok->get_error_message(), $fkey );
	}

	// Lock so two simultaneous requests can never create the same account or shop
	$locks = array( xsn_lock( 'email:' . strtolower( $email ) ) );
	if ( $vendor ) $locks[] = xsn_lock( 'shop:' . sanitize_title( $shop ) );
	register_shutdown_function( function () use ( &$locks ) { foreach ( $locks as $l ) { xsn_unlock( $l ); } } );
	if ( in_array( false, $locks, true ) ) xsn_fail( 'We are already processing this request. Please wait a few seconds.' );
	if ( email_exists( $email ) ) xsn_fail( 'An account with this email already exists. Please sign in instead.', 'email' );

	// Create the account
	$username = wc_create_new_customer_username( $email, array( 'first_name' => $first, 'last_name' => $last ) );
	$uid      = wc_create_new_customer( $email, $username, $pass, array( 'first_name' => $first, 'last_name' => $last ) );
	if ( is_wp_error( $uid ) ) xsn_fail( wp_strip_all_tags( $uid->get_error_message() ) );

	wp_set_current_user( $uid );
	if ( strlen( $token ) >= 16 ) set_transient( 'xsn_idem_' . $token, array( 'uid' => (int) $uid, 'eh' => md5( strtolower( $email ) ) ), 15 * MINUTE_IN_SECONDS );

	foreach ( array( 'first_name' => $first, 'last_name' => $last, 'phone' => $phone, 'address_1' => $addr, 'city' => $city, 'postcode' => $zip, 'country' => $country ) as $k => $v ) {
		update_user_meta( $uid, 'billing_' . $k, $v );
		if ( 'phone' !== $k ) update_user_meta( $uid, 'shipping_' . $k, $v );
	}
	update_user_meta( $uid, 'billing_email', $email );

	$photo_id = xsn_save_profile_photo( 'photo', $uid );
	if ( $photo_id ) update_user_meta( $uid, 'xsn_avatar_id', $photo_id );

	if ( $vendor ) {
		$user = new WP_User( $uid );
		$user->set_role( 'seller' );

		$slug = sanitize_title( $shop );
		$base = $slug;
		$i    = 1;
		while ( get_user_by( 'slug', $slug ) ) { $slug = $base . '-' . ( ++$i ); }
		wp_update_user( array( 'ID' => $uid, 'user_nicename' => $slug, 'display_name' => $first . ' ' . $last ) );

		$settings = array(
			'store_name'    => $shop,
			'social'        => array(),
			'payment'       => array(),
			'phone'         => $phone,
			'show_email'    => 'no',
			'address'       => array( 'street_1' => $addr, 'street_2' => '', 'city' => $city, 'zip' => $zip, 'state' => '', 'country' => $country ),
			'location'      => '',
			'banner'        => 0,
			'gravatar'      => $photo_id,
			'store_ppp'     => 12,
			'find_address'  => '',
			'show_more_ptab' => 'yes',
		);
		update_user_meta( $uid, 'dokan_profile_settings', $settings );
		update_user_meta( $uid, 'dokan_store_name', $shop );
		do_action( 'dokan_new_seller_created', $uid, $settings );
		update_user_meta( $uid, 'dokan_enable_selling', 'no' ); // selling stays OFF until an admin approves

		update_user_meta( $uid, 'xsn_kyc', array(
			'status'    => 'pending',
			'id_type'   => $id_type,
			'id_number' => $id_number,
			'id_expiry' => $id_expiry,
			'id_front'  => xsn_save_private_file( 'id_front', $uid ),
			'id_back'   => xsn_save_private_file( 'id_back', $uid ),
			'submitted' => time(),
			'reason'    => '',
		) );
		update_user_meta( $uid, 'xsn_kyc_status', 'pending' );

		xsn_mail(
			get_option( 'admin_email' ),
			'New vendor application: ' . $shop,
			'New vendor application',
			'<p><b>' . esc_html( $first . ' ' . $last ) . '</b> applied to open the shop <b>' . esc_html( $shop ) . '</b>.</p><p><a href="' . esc_url( admin_url( 'admin.php?page=xsn-vendors&uid=' . $uid ) ) . '">Review the application</a></p>'
		);
		xsn_mail(
			$email,
			'We received your vendor application',
			'Application received',
			'<p>Hello ' . esc_html( $first ) . ',</p><p>Thank you for applying to sell on XASSIDA SN. Our team is reviewing your details and identity documents. You will receive an email as soon as your boutique is approved.</p>'
		);
	}

	wc_set_customer_auth_cookie( $uid );
	wp_send_json_success( array( 'redirect' => wc_get_page_permalink( 'myaccount' ) ) );
}

/* ---------------------------------------------------------------
 * Profile photo as avatar
 * ------------------------------------------------------------- */
add_filter( 'pre_get_avatar_data', function ( $args, $id_or_email ) {
	$user = false;
	if ( is_numeric( $id_or_email ) ) {
		$user = get_user_by( 'id', (int) $id_or_email );
	} elseif ( $id_or_email instanceof WP_User ) {
		$user = $id_or_email;
	} elseif ( $id_or_email instanceof WP_Post ) {
		$user = get_user_by( 'id', (int) $id_or_email->post_author );
	} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
	}
	if ( $user ) {
		$aid = (int) get_user_meta( $user->ID, 'xsn_avatar_id', true );
		if ( $aid ) {
			$size = ! empty( $args['size'] ) ? (int) $args['size'] : 96;
			$url  = wp_get_attachment_image_url( $aid, array( $size, $size ) );
			if ( $url ) {
				$args['url']          = $url;
				$args['found_avatar'] = true;
			}
		}
	}
	return $args;
}, 10, 2 );

// Let customers change their photo from "Account details"
add_action( 'woocommerce_edit_account_form_tag', function () { echo ' enctype="multipart/form-data"'; } );
add_action( 'woocommerce_edit_account_form_start', function () {
	?>
	<div class="form-row form-row-wide acc-photo">
		<?php echo get_avatar( get_current_user_id(), 72 ); ?>
		<div>
			<label for="xsn_photo">Profile photo</label>
			<input type="file" name="xsn_photo" id="xsn_photo" accept="image/jpeg,image/png,image/webp">
			<small>JPG, PNG or WebP, up to 5 MB.</small>
		</div>
	</div>
	<?php
} );
add_action( 'woocommerce_save_account_details', function ( $user_id ) {
	if ( empty( $_FILES['xsn_photo']['name'] ) ) return; // phpcs:ignore WordPress.Security.NonceVerification
	$_FILES['photo'] = $_FILES['xsn_photo'];
	$ok = xsn_check_image( 'photo', false );
	if ( is_wp_error( $ok ) ) { wc_add_notice( $ok->get_error_message(), 'error' ); return; }
	$id = xsn_save_profile_photo( 'photo', $user_id );
	if ( $id ) update_user_meta( $user_id, 'xsn_avatar_id', $id );
} );

/* ---------------------------------------------------------------
 * Admin: Vendor applications
 * ------------------------------------------------------------- */
add_action( 'admin_menu', function () {
	$n     = xsn_pending_count();
	$label = 'Vendor applications' . ( $n ? ' <span class="awaiting-mod">' . (int) $n . '</span>' : '' );
	add_menu_page( 'Vendor applications', $label, 'manage_options', 'xsn-vendors', 'xsn_admin_page', 'dashicons-id-alt', 56 );
} );

function xsn_status_badge( $s ) {
	$c = array( 'pending' => '#b7791f', 'approved' => '#2f855a', 'rejected' => '#c53030' );
	$col = isset( $c[ $s ] ) ? $c[ $s ] : '#666';
	return '<span style="display:inline-block;padding:2px 10px;border-radius:99px;background:' . esc_attr( $col ) . ';color:#fff;font-size:12px;font-weight:600">' . esc_html( ucfirst( $s ) ) . '</span>';
}

function xsn_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	$uid = isset( $_GET['uid'] ) ? absint( $_GET['uid'] ) : 0;

	echo '<div class="wrap"><h1>Vendor applications</h1>';
	if ( isset( $_GET['done'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>Application ' . ( 'approve' === $_GET['done'] ? 'approved. The vendor has been emailed.' : 'rejected. The vendor has been emailed.' ) . '</p></div>';
	}

	if ( $uid && get_userdata( $uid ) ) {
		xsn_admin_detail( $uid );
	} else {
		$users = get_users( array( 'meta_key' => 'xsn_kyc_status', 'orderby' => 'registered', 'order' => 'DESC', 'number' => 200 ) );
		echo '<table class="widefat striped" style="margin-top:16px"><thead><tr><th>Applicant</th><th>Shop</th><th>Email</th><th>Phone</th><th>Submitted</th><th>Status</th><th></th></tr></thead><tbody>';
		if ( ! $users ) echo '<tr><td colspan="7">No applications yet.</td></tr>';
		foreach ( $users as $u ) {
			$s   = get_user_meta( $u->ID, 'dokan_profile_settings', true );
			$k   = get_user_meta( $u->ID, 'xsn_kyc', true );
			$st  = get_user_meta( $u->ID, 'xsn_kyc_status', true );
			$url = admin_url( 'admin.php?page=xsn-vendors&uid=' . $u->ID );
			echo '<tr><td>' . esc_html( $u->display_name ) . '</td><td>' . esc_html( is_array( $s ) && ! empty( $s['store_name'] ) ? $s['store_name'] : '-' ) . '</td><td>' . esc_html( $u->user_email ) . '</td><td>' . esc_html( get_user_meta( $u->ID, 'billing_phone', true ) ) . '</td><td>' . esc_html( is_array( $k ) && ! empty( $k['submitted'] ) ? wp_date( 'j M Y, H:i', $k['submitted'] ) : '-' ) . '</td><td>' . xsn_status_badge( $st ) . '</td><td><a class="button button-primary" href="' . esc_url( $url ) . '">Review</a></td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '</div>';
}

function xsn_admin_detail( $uid ) {
	$u   = get_userdata( $uid );
	$s   = get_user_meta( $uid, 'dokan_profile_settings', true );
	$k   = get_user_meta( $uid, 'xsn_kyc', true );
	$s   = is_array( $s ) ? $s : array();
	$k   = is_array( $k ) ? $k : array();
	$st  = get_user_meta( $uid, 'xsn_kyc_status', true );
	$adr = isset( $s['address'] ) && is_array( $s['address'] ) ? $s['address'] : array();
	$img = function ( $which ) use ( $uid ) {
		return esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=xsn_kyc_file&uid=' . $uid . '&k=' . $which ), 'xsn_kyc_file_' . $uid ) );
	};

	echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=xsn-vendors' ) ) . '">&larr; All applications</a></p>';
	echo '<h2>' . esc_html( $u->display_name ) . ' ' . xsn_status_badge( $st ) . '</h2>';
	echo '<table class="widefat striped" style="max-width:720px"><tbody>';
	$rows = array(
		'Shop name'  => isset( $s['store_name'] ) ? $s['store_name'] : '',
		'Email'      => $u->user_email,
		'Phone'      => get_user_meta( $uid, 'billing_phone', true ),
		'Address'    => trim( ( isset( $adr['street_1'] ) ? $adr['street_1'] : '' ) . ', ' . ( isset( $adr['city'] ) ? $adr['city'] : '' ) . ' ' . ( isset( $adr['zip'] ) ? $adr['zip'] : '' ) . ', ' . ( isset( $adr['country'] ) ? $adr['country'] : '' ), ' ,' ),
		'ID type'    => isset( $k['id_type'] ) ? $k['id_type'] : '',
		'ID number'  => isset( $k['id_number'] ) ? $k['id_number'] : '',
		'ID expiry'  => isset( $k['id_expiry'] ) ? $k['id_expiry'] : '',
		'Submitted'  => ! empty( $k['submitted'] ) ? wp_date( 'j M Y, H:i', $k['submitted'] ) : '',
	);
	foreach ( $rows as $label => $val ) {
		echo '<tr><th style="width:160px">' . esc_html( $label ) . '</th><td>' . esc_html( $val ) . '</td></tr>';
	}
	if ( ! empty( $k['reason'] ) ) echo '<tr><th>Rejection reason</th><td>' . esc_html( $k['reason'] ) . '</td></tr>';
	echo '</tbody></table>';

	echo '<h3>Identity documents</h3><div style="display:flex;gap:20px;flex-wrap:wrap">';
	$avatar_id = (int) get_user_meta( $uid, 'xsn_avatar_id', true );
	if ( $avatar_id ) echo '<div><p><b>Profile photo</b></p>' . wp_get_attachment_image( $avatar_id, 'medium', false, array( 'style' => 'border-radius:8px;max-width:260px;height:auto' ) ) . '</div>';
	if ( ! empty( $k['id_front'] ) ) echo '<div><p><b>ID front</b></p><a href="' . $img( 'front' ) . '" target="_blank" rel="noopener"><img src="' . $img( 'front' ) . '" style="max-width:340px;height:auto;border-radius:8px;border:1px solid #ccc" alt=""></a></div>';
	if ( ! empty( $k['id_back'] ) ) echo '<div><p><b>ID back</b></p><a href="' . $img( 'back' ) . '" target="_blank" rel="noopener"><img src="' . $img( 'back' ) . '" style="max-width:340px;height:auto;border-radius:8px;border:1px solid #ccc" alt=""></a></div>';
	echo '</div>';

	echo '<h3 style="margin-top:28px">Decision</h3><div style="display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start">';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'xsn_kyc_decide_' . $uid );
	echo '<input type="hidden" name="action" value="xsn_kyc_decide"><input type="hidden" name="uid" value="' . (int) $uid . '"><input type="hidden" name="decision" value="approve">';
	echo '<button class="button button-primary button-hero">Approve and enable selling</button></form>';

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'xsn_kyc_decide_' . $uid );
	echo '<input type="hidden" name="action" value="xsn_kyc_decide"><input type="hidden" name="uid" value="' . (int) $uid . '"><input type="hidden" name="decision" value="reject">';
	echo '<p><textarea name="reason" rows="3" cols="46" placeholder="Reason shown to the vendor (for example: ID photo is not readable)"></textarea></p>';
	echo '<button class="button">Reject application</button></form></div>';
}

// Secure viewer: only admins can open ID images
add_action( 'admin_post_xsn_kyc_file', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Not allowed.', 403 );
	$uid = isset( $_GET['uid'] ) ? absint( $_GET['uid'] ) : 0;
	check_admin_referer( 'xsn_kyc_file_' . $uid );
	$which = ( isset( $_GET['k'] ) && 'back' === $_GET['k'] ) ? 'id_back' : 'id_front';
	$kyc   = get_user_meta( $uid, 'xsn_kyc', true );
	$rel   = ( is_array( $kyc ) && ! empty( $kyc[ $which ] ) ) ? $kyc[ $which ] : '';
	$base  = realpath( xsn_private_dir() );
	$real  = $rel ? realpath( xsn_private_dir() . '/' . $rel ) : false;
	if ( ! $real || ! $base || 0 !== strpos( $real, $base ) || ! is_file( $real ) ) wp_die( 'File not found.', 404 );
	$ft = wp_check_filetype( $real );
	nocache_headers();
	header( 'Content-Type: ' . ( ! empty( $ft['type'] ) ? $ft['type'] : 'application/octet-stream' ) );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Content-Length: ' . filesize( $real ) );
	readfile( $real );
	exit;
} );

// Approve / reject
add_action( 'admin_post_xsn_kyc_decide', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Not allowed.', 403 );
	$uid = isset( $_POST['uid'] ) ? absint( $_POST['uid'] ) : 0;
	check_admin_referer( 'xsn_kyc_decide_' . $uid );
	$user = get_userdata( $uid );
	if ( ! $user ) wp_die( 'User not found.' );

	$approve = isset( $_POST['decision'] ) && 'approve' === $_POST['decision'];
	$kyc     = get_user_meta( $uid, 'xsn_kyc', true );
	$kyc     = is_array( $kyc ) ? $kyc : array();
	$first   = get_user_meta( $uid, 'first_name', true );

	if ( $approve ) {
		$done = false;
		if ( function_exists( 'dokan' ) ) {
			try {
				$v = dokan()->vendor->get( $uid );
				if ( $v && method_exists( $v, 'make_active' ) ) { $v->make_active(); $done = true; }
			} catch ( \Throwable $e ) { $done = false; }
		}
		if ( ! $done ) update_user_meta( $uid, 'dokan_enable_selling', 'yes' );
		$kyc['status'] = 'approved';
		$kyc['reason'] = '';
		$dash = function_exists( 'dokan_get_navigation_url' ) ? dokan_get_navigation_url() : wc_get_page_permalink( 'myaccount' );
		xsn_mail( $user->user_email, 'Your boutique is approved', 'You can start selling', '<p>Hello ' . esc_html( $first ) . ',</p><p>Great news: your seller account has been approved. You can now add products and manage orders from your vendor dashboard.</p><p><a href="' . esc_url( $dash ) . '">Open your vendor dashboard</a></p>' );
	} else {
		update_user_meta( $uid, 'dokan_enable_selling', 'no' );
		$reason        = isset( $_POST['reason'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reason'] ) ) : '';
		$kyc['status'] = 'rejected';
		$kyc['reason'] = $reason;
		xsn_mail( $user->user_email, 'About your vendor application', 'Application update', '<p>Hello ' . esc_html( $first ) . ',</p><p>Unfortunately we could not approve your seller application yet.</p>' . ( $reason ? '<p><b>Reason:</b> ' . esc_html( $reason ) . '</p>' : '' ) . '<p>Please contact our support team so we can help you complete the verification.</p>' );
	}

	$kyc['decided'] = time();
	update_user_meta( $uid, 'xsn_kyc', $kyc );
	update_user_meta( $uid, 'xsn_kyc_status', $kyc['status'] );

	if ( function_exists( 'xsn_bust_cache' ) ) xsn_bust_cache();
	wp_safe_redirect( admin_url( 'admin.php?page=xsn-vendors&done=' . ( $approve ? 'approve' : 'reject' ) ) );
	exit;
} );
