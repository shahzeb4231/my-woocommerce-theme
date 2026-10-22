<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<section class="page-hero">
	<div class="container container--narrow">
		<span class="eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
		<h1><?php the_title(); ?></h1>
	</div>
</section>
<section class="section page-body">
	<div class="container container--narrow">
		<article <?php post_class( 'entry' ); ?>>
			<?php if ( has_post_thumbnail() ) : ?><div class="entry-thumb"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
		<?php the_post_navigation(); ?>
		<?php if ( comments_open() || get_comments_number() ) comments_template(); ?>
	</div>
</section>
<?php endwhile; ?>
<?php get_footer(); ?>
