<?php
$acc_url = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
?>
<div class="mobile-menu" id="mobile-menu">
	<?php xsn_menu( 'primary', xsn_primary_fallback() ); ?>

	<div class="mm-account">
		<?php if ( is_user_logged_in() ) : ?>
			<a class="btn btn-ghost" href="<?php echo esc_url( $acc_url ); ?>">My account</a>
			<a class="btn btn-ghost" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">Log out</a>
		<?php else : ?>
			<a class="btn btn-ghost" href="<?php echo esc_url( $acc_url ); ?>">Sign in</a>
			<a class="btn btn-gold" href="<?php echo esc_url( add_query_arg( 'action', 'register', $acc_url ) ); ?>">Create account</a>
		<?php endif; ?>
	</div>

	<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/#booking' ) ); ?>">Book Now</a>
</div>
