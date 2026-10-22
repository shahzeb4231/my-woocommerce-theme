<?php
$has_img = file_exists( XSN_DIR . '/assets/images/app-mockup.png' );
?>
<section class="section" id="app">
	<div class="container app-grid">

		<div class="reveal">
			<span class="eyebrow">Mobile App</span>
			<h2>The whole platform, <span class="text-gold">in your pocket</span></h2>
			<p>Book rides, send parcels, shop the marketplace and track everything from one beautifully simple app.</p>

			<ul class="app-list">
				<li><b>Live map tracking</b><span>Watch your driver arrive in real time.</span></li>
				<li><b>Instant updates</b><span>Push alerts at every step of your order.</span></li>
				<li><b>Cashless wallet</b><span>Pay securely and keep every receipt.</span></li>
			</ul>

			<div class="store-btns">
				<a class="store" href="#">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/></svg>
					<span><small>Download on the</small><b>App Store</b></span>
				</a>
				<a class="store" href="#">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3.5v17a1 1 0 0 0 1.5.9l14-8.5a1 1 0 0 0 0-1.8l-14-8.5A1 1 0 0 0 5 3.5z"/></svg>
					<span><small>Get it on</small><b>Google Play</b></span>
				</a>
			</div>
		</div>

		<div class="app-visual reveal" style="--d:.15s">
			<?php if ( $has_img ) : ?>
				<img src="<?php echo xsn_img( 'app-mockup.png' ); ?>" alt="XASSIDA SN mobile app" loading="lazy">
			<?php else : ?>
				<div class="phone" aria-hidden="true">
					<div class="phone-screen">
						<span class="ph-bar"></span>
						<div class="ph-map"><i></i></div>
						<span class="ph-line w70"></span><span class="ph-line w50"></span>
						<span class="ph-btn"></span>
					</div>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>
