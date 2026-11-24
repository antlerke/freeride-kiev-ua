<?php
/** Запасний шаблон. На цьому сайті майже не використовується. */
declare( strict_types = 1 );
get_header();
?>
<section class="wrap" style="padding: 48px 24px">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p>Нічого не знайдено.</p>
	<?php endif; ?>
</section>
<?php get_footer();
