<?php get_header(); ?>
<section class="section">
	<div class="container empty-state">
		<span class="eyebrow">Error 404</span>
		<h1>Page not found</h1>
		<p>The page you are looking for does not exist or has moved.</p>
		<p><a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to home</a></p>
		<?php get_search_form(); ?>
	</div>
</section>
<?php get_footer(); ?>
