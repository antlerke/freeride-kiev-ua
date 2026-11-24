<?php
/**
 * Сторінка курсу.
 */
declare( strict_types = 1 );
get_header();

while ( have_posts() ) :
	the_post();
	$id    = get_the_ID();
	$hours = fr_course_hours( $id );
	$price = (int) get_field( 'price', $id );
	?>
	<section class="hero hero-page">
		<div class="hero-media<?php echo has_post_thumbnail() ? '' : ' ph'; ?>">
			<?php echo has_post_thumbnail() ? get_the_post_thumbnail( $id, 'fr-hero', [ 'alt' => '' ] ) : '[ФОТО курсу]'; ?>
		</div>
		<div class="hero-veil"></div>
		<div class="wrap hero-in">
			<?php fr_breadcrumbs(); ?>
			<h1><span class="slash"><i></i><i></i></span><?php the_title(); ?></h1>
			<?php if ( $lede = get_field( 'summary', $id ) ) : ?>
				<p class="lede"><?php echo esc_html( $lede ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="wrap" style="padding: 0 24px 48px">
		<div class="rates" data-rev-group="up">
			<div class="rate"><b><?php echo esc_html( (string) get_field( 'code', $id ) ); ?></b><s>код курсу</s></div>
			<div class="rate"><b><?php echo esc_html( (string) $hours ); ?> год</b><s>усього занять</s></div>
			<div class="rate"><b><?php echo esc_html( number_format_i18n( $price ) ); ?> грн</b><s>вартість пакета</s></div>
		</div>

		<div class="entry">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
