<?php
/**
 * Каталог курсів: /motocourses/
 *
 * Адреса успадкована зі старого сайту й затверджена замовником.
 */
declare( strict_types = 1 );
get_header();

$intro = get_field( 'archive_intro', 'option' );
?>
<section class="hero hero-page">
	<div class="hero-media ph">[ФОТО: шолом крупним планом]</div>
	<div class="hero-veil"></div>
	<div class="wrap hero-in">
		<nav class="crumbs" aria-label="Навігація сторінками">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Головна</a><span>›</span>Курси
		</nav>
		<h1><span class="slash"><i></i><i></i></span><?php echo esc_html( (string) ( get_field( 'archive_title', 'option' ) ?: 'Програми навчання' ) ); ?></h1>
		<?php if ( $intro ) : ?>
			<p class="lede"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="wrap" id="levels" style="padding: 0 24px 48px">
	<?php if ( have_posts() ) : ?>
		<div class="progs" data-rev-group="up">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'parts/course-card' ); ?>
			<?php endwhile; ?>
		</div>
	<?php else : ?>
		<p>Курсів поки немає. Додайте їх у розділі «Курси» в адмінці.</p>
	<?php endif; ?>
</section>

<?php get_footer();
