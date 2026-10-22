<?php
$steps = array(
	array( 'Book',            'Choose a ride, delivery or shipment in seconds, with the price shown upfront.' ),
	array( 'Driver Assigned', 'A nearby verified driver accepts and heads straight to you.' ),
	array( 'Live Tracking',   'Follow every move in real time with precise GPS and a live ETA.' ),
	array( 'Delivered',       'Arrive or receive safely, then pay securely and rate your experience.' ),
);
?>
<section class="section section--white" id="how-it-works">
	<div class="container">
		<div class="section-head reveal">
			<span class="eyebrow">How It Works</span>
			<h2>Four simple steps, <span class="text-gold">zero friction</span></h2>
			<p>A seamless journey from doorstep to doorstep, engineered to feel effortless every single time.</p>
		</div>

		<div class="steps">
			<?php foreach ( $steps as $i => $s ) : ?>
				<div class="step reveal" style="--d:<?php echo esc_attr( $i * 0.1 ); ?>s">
					<div class="step-num"><?php echo (int) ( $i + 1 ); ?></div>
					<h3><?php echo esc_html( $s[0] ); ?></h3>
					<p><?php echo esc_html( $s[1] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>