<?php get_header(); ?>

<?php
$sections = array(
	'hero', 'stats', 'services', 'how-it-works', 'tracking', 'marketplace',
	'flash-sale', 'categories', 'tailored', 'boutiques',
	'booking', 'fleet', 'why-us', 'testimonials', 'app-download', 'faq', 'contact',
);
foreach ( $sections as $section ) {
	get_template_part( 'template-parts/sections/' . $section );
}
?>

<?php get_footer(); ?>
