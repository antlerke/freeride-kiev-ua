<?php
/**
 * Template Name: Галерея
 *
 * Шапка сторінки збирається з полів, тіло — з редактора. Блоки
 * за макетом додаються сюди поступово; те, що вже є, редагується
 * з адмінки як звичайний текст.
 */

declare( strict_types = 1 );
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="hero hero-page">
		<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО сторінки]'; ?>
		</div>
		<div class="hero-veil"></div>
		<div class="wrap hero-in">
			<?php fr_breadcrumbs(); ?>
			<h1><span class="slash"><i></i><i></i></span><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="wrap" style="padding: 0 24px 56px">
		<div class="entry"><?php the_content(); ?></div>
	</section>
	<?php
endwhile;

get_footer();
