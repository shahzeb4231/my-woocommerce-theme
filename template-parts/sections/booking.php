<?php
// Apna WhatsApp number yahan likhen (country code ke saath, + ya spaces ke baghair)
$wa = '221770000000';
?>
<section class="section section--dark booking" id="booking">
	<div class="container booking-grid">

		<div class="reveal">
			<span class="eyebrow">Book &amp; Ride</span>
			<h2>Your next trip is <span class="text-gold">one tap away</span></h2>
			<p>Set your pickup and destination, choose a vehicle and go. Transparent fares, verified drivers and live tracking included.</p>
			<div class="book-stats">
				<div><strong>4.9</strong><span>App rating</span></div>
				<div><strong>3 min</strong><span>Avg. pickup</span></div>
				<div><strong>100%</strong><span>Safe rides</span></div>
			</div>
		</div>

		<form class="book-card reveal" style="--d:.15s" data-booking data-wa="<?php echo esc_attr( $wa ); ?>">
			<div class="field">
				<label for="b-pickup">Pickup location</label>
				<input id="b-pickup" name="pickup" type="text" placeholder="Touba, Quartier Darou Marnane" required>
			</div>
			<div class="field">
				<label for="b-dest">Destination</label>
				<input id="b-dest" name="dest" type="text" placeholder="Dakar, Plateau" required>
			</div>
			<div class="field-row">
				<div class="field"><label for="b-date">Date</label><input id="b-date" name="date" type="date" required></div>
				<div class="field"><label for="b-time">Time</label><input id="b-time" name="time" type="time" required></div>
				<div class="field"><label for="b-pax">Passengers</label>
					<select id="b-pax" name="pax">
						<?php for ( $n = 1; $n <= 6; $n++ ) : ?><option value="<?php echo (int) $n; ?>"><?php echo (int) $n; ?></option><?php endfor; ?>
					</select>
				</div>
			</div>

			<div class="field">
				<span class="label">Vehicle</span>
				<div class="vehicle-tabs" role="group" aria-label="Vehicle type">
					<button type="button" class="vtab" data-type="economy" aria-pressed="false">Economy</button>
					<button type="button" class="vtab is-active" data-type="taxi" aria-pressed="true">Taxi</button>
					<button type="button" class="vtab" data-type="suv" aria-pressed="false">SUV</button>
					<button type="button" class="vtab" data-type="luxury" aria-pressed="false">Luxury</button>
				</div>
			</div>

			<div class="fare-row">
				<span>Estimated fare</span>
				<strong data-fare>3 200 CFA</strong>
			</div>
			<button class="btn btn-gold btn-block" type="submit">Book Ride &rarr;</button>
			<p class="book-note">Your request opens in WhatsApp so a dispatcher can confirm it instantly.</p>
		</form>

	</div>
</section>
