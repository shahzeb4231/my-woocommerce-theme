<?php
// Ye sample reviews hain. Launch se pehle asli customers ke reviews se badal den.
$reviews = array(
	array( 'Amadou Diop',   'Dakar',   'My airport pickup was right on time and the driver was polite and professional. Booking took seconds.' ),
	array( 'Aissatou Fall', 'Touba',   'I send parcels to my family every week. Live tracking gives me complete peace of mind.' ),
	array( 'Fatou Ndiaye',  'Thies',   'The marketplace is beautiful and my order arrived faster than I expected, perfectly packed.' ),
	array( 'Cheikh Sow',    'Saint-Louis', 'International shipping with customs support saved me so much stress. Highly recommended.' ),
);
?>
<section class="section section--white" id="testimonials">
	<div class="container">
		<div class="section-head reveal">
			<span class="eyebrow">Testimonials</span>
			<h2>Loved across Senegal <span class="text-gold">&amp; beyond</span></h2>
			<p>Thousands of riders, shoppers and businesses trust XASSIDA SN every day.</p>
		</div>
		<div class="grid grid-4">
			<?php foreach ( $reviews as $i => $r ) :
				get_template_part( 'template-parts/cards/testimonial-card', null, array(
					'name' => $r[0], 'place' => $r[1], 'text' => $r[2], 'delay' => $i * 0.08,
				) );
			endforeach; ?>
		</div>
	</div>
</section>
