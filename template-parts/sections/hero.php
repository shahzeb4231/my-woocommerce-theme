<?php
$has_img = file_exists( XSN_DIR . '/assets/images/hero.png' );
?>
<section class="hero" id="home">
	<div class="container hero-inner">

		<div class="hero-copy reveal">
			<span class="eyebrow eyebrow--pill">Now serving Senegal &middot; Shipping worldwide</span>
			<h1>One platform for <span class="text-gold">transport,</span> shopping &amp; delivery.</h1>
			<p class="hero-lead">Passenger rides, parcel and food delivery, courier and cargo, international shipping and a premium marketplace, all in one luxurious experience built for Senegal and beyond.</p>

			<div class="hero-cta">
				<a class="btn btn-gold" href="#booking">Book a Ride</a>
				<a class="btn btn-ghost" href="#marketplace">Shop Now</a>
			</div>

			<div class="hero-chips">
				<span class="chip"><i></i>Verified drivers</span>
				<span class="chip"><i></i>Live GPS tracking</span>
				<span class="chip"><i></i>Secure payments</span>
			</div>
		</div>

		<div class="hero-visual reveal" style="--d:.15s">
			<div class="hero-art">
				<?php if ( $has_img ) : ?>
					<img src="<?php echo xsn_img( 'hero.png' ); ?>" alt="XASSIDA SN transport and delivery" fetchpriority="high">
				<?php else : ?>
					<span class="ring r1"></span><span class="ring r2"></span>
					<div class="core">X</div>
				<?php endif; ?>
			</div>

			<div class="float-card f1"><span class="dot">&#10003;</span><div><b>Ride booked</b><small>Dourtoul &rarr; Dakar</small></div></div>
			<div class="float-card f2"><span class="dot">&#9889;</span><div><b>Package in transit</b><small>Arrives in 12 min</small></div></div>
			<div class="float-card f3"><span class="dot">&#9733;</span><div><b>Order delivered</b><small>Rated 5.0 by customer</small></div></div>
		</div>

	</div>
</section>