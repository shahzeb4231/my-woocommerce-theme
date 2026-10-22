<?php
$faqs = array(
	array( 'Where does XASSIDA SN operate?', 'We operate across Senegal, including Touba, Dakar, Thies and more, with international shipping and a marketplace that serves customers in the United States and beyond.' ),
	array( 'How do I book a ride or delivery?', 'Choose your pickup and destination in the booking form, pick a vehicle and confirm. A dispatcher confirms it with you right away and a verified driver is assigned.' ),
	array( 'What payment methods can I use?', 'We support secure online payments and cash where available. Available methods are shown at checkout.' ),
	array( 'Can I ship a parcel or bulky cargo abroad?', 'Yes. We handle international parcels and cargo with customs support. Shipping costs and restrictions are shown before you confirm.' ),
	array( 'How do I track my order?', 'Enter your tracking number in the Live Tracking section to see your status and estimated arrival at every step.' ),
	array( 'How do I become a driver or seller?', 'Use the contact form below or the Become a Seller option on the marketplace. Our team reviews every application and gets back to you.' ),
);

$schema = array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array() );
foreach ( $faqs as $f ) {
	$schema['mainEntity'][] = array( '@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ) );
}
?>
<section class="section section--white" id="faq">
	<div class="container faq-wrap">
		<div class="section-head reveal">
			<span class="eyebrow">FAQ</span>
			<h2>Questions? <span class="text-gold">Answered</span></h2>
			<p>Everything you need to know about riding, shipping and shopping with XASSIDA SN.</p>
		</div>

		<div class="faq-list reveal" data-faq>
			<?php foreach ( $faqs as $i => $f ) : ?>
				<div class="faq-item">
					<h3>
						<button class="faq-q" type="button" aria-expanded="false" aria-controls="faq-a-<?php echo (int) $i; ?>" id="faq-q-<?php echo (int) $i; ?>">
							<span><?php echo esc_html( $f[0] ); ?></span><i aria-hidden="true"></i>
						</button>
					</h3>
					<div class="faq-a" id="faq-a-<?php echo (int) $i; ?>" role="region" aria-labelledby="faq-q-<?php echo (int) $i; ?>">
						<p><?php echo esc_html( $f[1] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
</section>
