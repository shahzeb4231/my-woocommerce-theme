<?php
$fleet = array(
	array( 'Luxury Taxi',    'Passenger',  '4 seats', array( 'Air-conditioned comfort', 'Verified professional driver', 'Free water and phone charger' ), 'fleet-1.png' ),
	array( 'Comfort Sedan',  'Passenger',  '4 seats', array( 'Smooth city and highway rides', 'Spacious boot for luggage', 'Fixed, transparent fares' ),       'fleet-2.png' ),
	array( 'Family SUV',     'Passenger',  '6 seats', array( 'Room for the whole family', 'Extra luggage space', 'Child seats on request' ),                    'fleet-3.png' ),
	array( 'Cargo Van',      'Delivery',   '1.2 t',   array( 'Furniture and bulk deliveries', 'Loading assistance included', 'GPS tracked end to end' ),              'fleet-4.png' ),
	array( 'Express Scooter','Delivery',   'Parcels', array( 'Fastest city dispatch', 'Documents and small parcels', 'Live status for every drop' ),                   'fleet-5.png' ),
);
?>
<section class="section" id="fleet">
	<div class="container">
		<div class="section-head reveal">
			<span class="eyebrow">Our Fleet</span>
			<h2>A vehicle for <span class="text-gold">every mission</span></h2>
			<p>Every vehicle is inspected, maintained to premium standards and driven by professionals.</p>
		</div>

		<div class="fleet reveal" data-fleet>
			<?php foreach ( $fleet as $i => $v ) :
				$img = 'assets/images/' . $v[4];
				$has = file_exists( XSN_DIR . '/' . $img );
			?>
				<div class="fleet-slide<?php echo 0 === $i ? ' is-active' : ''; ?>">
					<div class="fleet-info">
						<span class="tag"><?php echo esc_html( $v[1] ); ?></span>
						<h3><?php echo esc_html( $v[0] ); ?></h3>
						<ul class="fleet-feats">
							<li><b><?php echo esc_html( $v[2] ); ?></b></li>
							<?php foreach ( $v[3] as $f ) : ?><li><?php echo esc_html( $f ); ?></li><?php endforeach; ?>
						</ul>
						<a class="btn btn-primary" href="#booking">Book this vehicle</a>
					</div>
					<div class="fleet-art">
						<?php if ( $has ) : ?>
							<img src="<?php echo xsn_img( $v[4] ); ?>" alt="<?php echo esc_attr( $v[0] ); ?>" loading="lazy">
						<?php else : ?>
							<svg viewBox="0 0 320 160" aria-hidden="true">
								<path d="M30 112l16-42a16 16 0 0 1 15-10h128a24 24 0 0 1 18 8l32 28 26 6a10 10 0 0 1 8 10v10H30z" fill="rgba(255,255,255,.1)" stroke="#e3c77d" stroke-width="3" stroke-linejoin="round"/>
								<path d="M70 68h60v28H62zM140 68h54l22 28h-76z" fill="rgba(227,199,125,.25)"/>
								<circle cx="84" cy="124" r="18" fill="#072b20" stroke="#e3c77d" stroke-width="4"/>
								<circle cx="240" cy="124" r="18" fill="#072b20" stroke="#e3c77d" stroke-width="4"/>
							</svg>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>

			<div class="fleet-ctrl">
				<button class="ctrl" type="button" data-prev aria-label="Previous vehicle">&larr;</button>
				<div class="fleet-dots">
					<?php foreach ( $fleet as $i => $v ) : ?>
						<button class="fleet-dot<?php echo 0 === $i ? ' is-active' : ''; ?>" type="button" aria-label="<?php echo esc_attr( $v[0] ); ?>"></button>
					<?php endforeach; ?>
				</div>
				<button class="ctrl" type="button" data-next aria-label="Next vehicle">&rarr;</button>
			</div>
		</div>
	</div>
</section>
