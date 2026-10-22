<?php
$steps = array(
	array( 'Pickup placed',     'Driver confirmed your order' ),
	array( 'In transit',        'On the way to the destination' ),
	array( 'Out for delivery',  'Arriving at your door shortly' ),
	array( 'Delivered',         'Signed and completed' ),
);
?>
<section class="section" id="tracking">
	<div class="container tracking-grid">

		<div class="reveal">
			<span class="eyebrow">Live Tracking</span>
			<h2>Track every ride &amp; <span class="text-gold">parcel in real time</span></h2>
			<p>Enter your tracking number to watch your driver move on the map, with a precise ETA and status at every step.</p>

			<form class="track-form" data-track>
				<label class="screen-reader-text" for="track-id">Tracking number</label>
				<input id="track-id" type="text" placeholder="e.g. XSN-2026-48215" autocomplete="off">
				<button class="btn btn-primary" type="submit">Track</button>
			</form>
			<p class="track-note" data-track-note aria-live="polite"></p>

			<div class="timeline">
				<?php foreach ( $steps as $i => $s ) : ?>
					<div class="tl-item<?php echo 0 === $i ? ' is-done' : ( 1 === $i ? ' is-active' : '' ); ?>">
						<span class="tl-dot"></span>
						<div><b><?php echo esc_html( $s[0] ); ?></b><small><?php echo esc_html( $s[1] ); ?></small></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="track-map reveal" style="--d:.15s">
			<div class="map-top">
				<span class="map-id" data-track-id>XSN-2026-48215</span>
				<span class="map-live"><i></i>Live</span>
			</div>
			<svg viewBox="0 0 520 300" role="img" aria-label="Route preview">
				<defs>
					<pattern id="grid" width="26" height="26" patternUnits="userSpaceOnUse"><path d="M26 0H0V26" fill="none" stroke="rgba(255,255,255,.06)"/></pattern>
				</defs>
				<rect width="520" height="300" fill="url(#grid)"/>
				<path id="route" d="M30 240 C 120 240, 140 80, 260 140 S 420 250, 490 60" fill="none" stroke="rgba(201,162,75,.35)" stroke-width="3"/>
				<path class="route-run" d="M30 240 C 120 240, 140 80, 260 140 S 420 250, 490 60" fill="none" stroke="#e3c77d" stroke-width="3" stroke-linecap="round"/>
				<circle cx="30" cy="240" r="7" fill="#e3c77d"/>
				<circle cx="490" cy="60" r="9" fill="none" stroke="#e3c77d" stroke-width="3"/>
				<circle r="9" fill="#fff"><animateMotion dur="7s" repeatCount="indefinite"><mpath href="#route"/></animateMotion></circle>
			</svg>
			<div class="map-eta"><small>Estimated arrival</small><b data-eta>12 min</b></div>
		</div>

	</div>
</section>
