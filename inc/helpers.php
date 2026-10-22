<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** URL of a file inside /assets/images/ */
function xsn_img( $file ) {
	return esc_url( XSN_URI . '/assets/images/' . ltrim( $file, '/' ) );
}

/** Menu with a hard-coded fallback so the site never looks empty */
function xsn_menu( $location, $fallback = array(), $class = '' ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu( array( 'theme_location' => $location, 'container' => false, 'menu_class' => $class, 'depth' => 1 ) );
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $fallback as $label => $url ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

function xsn_primary_fallback() {
	return array(
		'Home'        => home_url( '/' ),
		'Services'    => home_url( '/#services' ),
		'Marketplace' => home_url( '/#marketplace' ),
		'Track Order' => home_url( '/#tracking' ),
		'Drivers'     => home_url( '/#why-us' ),
		'About'       => home_url( '/#why-us' ),
		'Contact'     => home_url( '/#contact' ),
	);
}