<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'XSN_VERSION', '1.0.0' );
define( 'XSN_DIR', get_template_directory() );
define( 'XSN_URI', get_template_directory_uri() );

// DEV ONLY: lets the admin/owner buy their own products while testing. Remove this line before launch.
define( 'XSN_ALLOW_OWN_PURCHASE', true );

// When the site sits behind Cloudflare (live hosting), uncomment the next line so visitor IPs are read correctly:
// define( 'XSN_BEHIND_CLOUDFLARE', true );

foreach ( array( 'setup', 'helpers', 'security', 'performance', 'enqueue', 'woocommerce', 'account', 'onboarding', 'wishlist', 'post-types', 'customizer', 'contact' ) as $file ) {
	$path = XSN_DIR . "/inc/{$file}.php";
	if ( file_exists( $path ) ) require_once $path;
}
