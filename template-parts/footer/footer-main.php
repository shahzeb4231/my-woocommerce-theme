<?php
// Apne asli social links yahan likhen
$socials = array(
	array( 'Facebook',  '#', '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>' ),
	array( 'Instagram', '#', '<rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.4A4 4 0 1 1 12.6 8 4 4 0 0 1 16 11.4z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>' ),
	array( 'WhatsApp',  '#', '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22z"/><g transform="translate(6.6 6.6) scale(.46)"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></g>' ),
	array( 'TikTok',    '#', '<path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/>' ),
);
?>
<footer class="site-footer">
	<div class="container">
		<div class="footer-grid">

			<div>
				<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="logo-mark">X</span>
					<span>XASSIDA <small>SN</small></span>
				</a>
				<p>One premium platform for transport, delivery and shopping, proudly serving Senegal and the diaspora.</p>
			</div>

			<div>
				<h4>Services</h4>
				<?php xsn_menu( 'footer_services', array(
					'Ride Booking'           => home_url( '/#booking' ),
					'Courier Delivery'       => home_url( '/#services' ),
					'Food Delivery'          => home_url( '/#services' ),
					'Cargo Transport'        => home_url( '/#services' ),
					'International Shipping' => home_url( '/#services' ),
				) ); ?>
			</div>

			<div>
				<h4>Company</h4>
				<?php xsn_menu( 'footer_company', array(
					'Marketplace' => home_url( '/#marketplace' ),
					'Drivers'     => home_url( '/#why-us' ),
					'About Us'    => home_url( '/#why-us' ),
					'Careers'     => home_url( '/#contact' ),
				) ); ?>
			</div>

			<div>
				<h4>Support</h4>
				<?php xsn_menu( 'footer_support', array(
					'Help Center' => home_url( '/#faq' ),
					'Track Order' => home_url( '/#tracking' ),
					'Contact'     => home_url( '/#contact' ),
					'Privacy'     => home_url( '/privacy-policy/' ),
					'Terms'       => home_url( '/terms/' ),
				) ); ?>
			</div>

		</div>

		<div class="footer-bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> XASSIDA SN. All rights reserved.</span>
			<div class="socials">
				<?php foreach ( $socials as $i => $s ) : ?>
					<a class="social" href="<?php echo esc_url( $s[1] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $s[0] ); ?>" style="--i:<?php echo (int) $i; ?>">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $s[2]; // phpcs:ignore -- static SVG ?></svg>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</footer>
