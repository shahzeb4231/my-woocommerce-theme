<?php
$stats = array(
	array( 10000, '+', 'Happy customers' ),
	array( 500,   '+', 'Verified drivers' ),
	array( 24,    '/7', 'Live support' ),
	array( 60,    '+', 'Cities covered' ),
);
?>
<section class="stats">
	<div class="container">
		<div class="stats-bar reveal">
			<?php foreach ( $stats as $s ) : ?>
				<div class="stat">
					<strong><span data-count="<?php echo (int) $s[0]; ?>">0</span><?php echo esc_html( $s[1] ); ?></strong>
					<span><?php echo esc_html( $s[2] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>