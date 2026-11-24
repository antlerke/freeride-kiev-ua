<?php
/**
 * Template Name: Команда
 *
 * Той самий список інструкторів, що й на «Про нас», просто повний.
 */

declare( strict_types = 1 );
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="hero hero-page">
		<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО: інструктори школи]'; ?>
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

	<?php $mates = fr_mates(); ?>
	<section class="wrap" style="padding: 0 24px 48px">
		<?php if ( $mates ) : ?>
			<div class="team" data-rev-group="up">
				<?php foreach ( $mates as $m ) { get_template_part( 'parts/mate-card', null, [ 'mate' => $m ] ); } ?>
			</div>
		<?php else : ?>
			<p>Інструкторів ще не додано. Розділ «Інструктори» в адмінці.</p>
		<?php endif; ?>
	</section>

	<?php if ( get_the_content() ) : ?>
		<section class="wrap" style="padding: 0 24px 48px"><div class="entry"><?php the_content(); ?></div></section>
	<?php endif; ?>

	<section class="final" id="final">
		<div class="final-media ph">[ФОТО: мотоцикліст на серпантині]</div>
		<div class="final-veil"></div>
		<div class="wrap final-in">
			<h2>Хочете потрапити саме до цього інструктора?</h2>
			<p>Скажіть про це при записі — підберемо зручний час.</p>
			<?php get_template_part( 'parts/cta-trial' ); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
