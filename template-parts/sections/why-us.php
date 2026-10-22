<?php
$has_img = file_exists( XSN_DIR . '/assets/images/warehouse.jpg' );

// Asli numbers client se confirm kar ke yahan badlen
$badge_main = array( '99.4%', 'On-time delivery' );
$badge_alt  = array( '2M+', 'Deliveries completed' );

$feats = array(
	array( 'Fast Delivery',          'Smart routing gets every order to you quickly.',          '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>' ),
	array( 'Verified Drivers',       'Background-checked, trained professionals only.',          '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>' ),
	array( 'GPS Tracking',           'Follow every trip and parcel live on the map.',            '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>' ),
	array( 'Affordable Pricing',     'Clear upfront fares with no hidden charges.',              '<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><line x1="7" y1="7" x2="7.01" y2="7"/>' ),
	array( '24/7 Support',           'Real people ready to help, any hour of the day.',          '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>' ),
	array( 'Secure Payments',        'Protected checkout with trusted payment methods.',         '<rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>' ),
	array( 'International Shipping', 'Worldwide delivery with customs support.',                 '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>' ),
	array( 'Insured Cargo',           'Your goods stay protected from pickup to doorstep.',       '<path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.3 7 12 12 20.7 7"/><line x1="12" y1="22" x2="12" y2="12"/>' ),
);
?>
<section class="section" id="why-us">
	<div class="container why-grid">

		<div class="why-media reveal">
			<div class="why-img">
				<?php if ( $has_img ) : ?>
					<img src="<?php echo xsn_img( 'warehouse.jpg' ); ?>" alt="XASSIDA SN logistics warehouse" loading="lazy">
				<?php else : ?>
					<span class="why-ph" aria-hidden="true"></span>
				<?php endif; ?>
			</div>
			<div class="why-badge b1"><strong><?php echo esc_html( $badge_main[0] ); ?></strong><span><?php echo esc_html( $badge_main[1] ); ?></span></div>
			<div class="why-badge b2"><strong><?php echo esc_html( $badge_alt[0] ); ?></strong><span><?php echo esc_html( $badge_alt[1] ); ?></span></div>
		</div>

		<div class="reveal" style="--d:.12s">
			<span class="eyebrow">Why Choose Us</span>
			<h2>Built on trust, <span class="text-gold">engineered for speed</span></h2>
			<p>A community-focused platform that treats every ride, parcel and order with premium care.</p>

			<div class="feat-grid">
				<?php foreach ( $feats as $f ) : ?>
					<div class="feat">
						<span class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $f[2]; // phpcs:ignore -- static SVG ?></svg></span>
						<div><h3><?php echo esc_html( $f[0] ); ?></h3><p><?php echo esc_html( $f[1] ); ?></p></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

	</div>
</section>
