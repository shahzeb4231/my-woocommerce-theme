<?php
$items = array(
	array( 'Passenger Transport', 'Comfortable, safe rides in the city or across regions, at the tap of a button.',
		'<path d="M5 16l1.5-5a2 2 0 0 1 1.9-1.4h7.2a2 2 0 0 1 1.9 1.4L19 16"/><rect x="3" y="16" width="18" height="4" rx="1.5"/><circle cx="7.5" cy="18" r=".6"/><circle cx="16.5" cy="18" r=".6"/>' ),
	array( 'Airport Pickup', 'Scheduled pickups with flight tracking and meet-and-greet drivers.',
		'<path d="M2 16l20-6-3-3-6 2-6-4-2 1 4 5-4 2-3-1z"/>' ),
	array( 'Courier Delivery', 'Same-day documents and parcels handled by trusted couriers.',
		'<path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/>', true ),
	array( 'Marketplace Shopping', 'Fashion, electronics, home and more delivered straight to your door.',
		'<path d="M6 7h12l1 13H5L6 7z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/>' ),
	array( 'Cargo Transport', 'Heavy loads moved reliably with fleet-grade vehicles and tracking.',
		'<rect x="2" y="7" width="12" height="9" rx="1"/><path d="M14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>' ),
	array( 'International Shipping', 'Worldwide freight with full customs support from Dakar to New York.',
		'<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>', true ),
	array( 'Food Delivery', 'Hot meals from your favourite restaurants, delivered fast and fresh.',
		'<path d="M4 3v8a3 3 0 0 0 3 3v7M7 3v6M10 3v8a3 3 0 0 1-3 3"/><path d="M17 3c-2 2-2 6 0 8v10"/>' ),
	array( 'Scooter Express', 'Lightning-fast city dispatch for urgent, small deliveries.',
		'<circle cx="6" cy="17" r="3"/><circle cx="18" cy="17" r="3"/><path d="M6 17h6l3-8h3M13 9H9"/>' ),
	array( 'Furniture Delivery', 'Careful handling and setup for large household items.',
		'<path d="M4 11V8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3"/><path d="M2 14a2 2 0 0 1 4 0v2h12v-2a2 2 0 0 1 4 0v4H2v-4z"/>' ),
	array( 'Business Logistics', 'Scalable delivery solutions and contracts for growing companies.',
		'<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>' ),
);
?>
<section class="section" id="services">
	<div class="container">
		<div class="section-head reveal">
			<span class="eyebrow">Our Services</span>
			<h2>Everything you move, <span class="text-gold">in one place</span></h2>
			<p>From a quick ride to international freight, XASSIDA SN brings transport, delivery and shopping together under one premium platform.</p>
		</div>

		<div class="grid grid-5">
			<?php foreach ( $items as $i => $it ) :
				get_template_part( 'template-parts/cards/service-card', null, array(
					'title'    => $it[0],
					'text'     => $it[1],
					'icon'     => $it[2],
					'featured' => ! empty( $it[3] ),
					'delay'    => ( $i % 5 ) * 0.07,
				) );
			endforeach; ?>
		</div>
	</div>
</section>