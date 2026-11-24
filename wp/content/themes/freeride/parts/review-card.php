<?php
/**
 * Картка текстового відгуку.
 *
 * @var WP_Post $args['review']
 */
declare( strict_types = 1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Увага: у частинах шаблону не можна заводити змінну $id. WordPress
   оголошує її глобальною в load_template(), тож присвоєння тут мовчки
   перезаписує $id у файлі, який цю частину підключив, і там
   «зникають» усі наступні поля. Тому скрізь $rid, $cid, $pid. */
$r = $args['review'] ?? null;
if ( ! $r instanceof WP_Post ) {
	return;
}
$rid     = $r->ID;
$rate   = max( 1, min( 5, (int) get_field( 'rating', $rid ) ) );
$course = (int) get_field( 'course', $rid );
$mate   = (int) get_field( 'mate', $rid );
$given  = (string) get_field( 'given', $rid );
$shot   = (int) get_field( 'photo', $rid );

// Підпис під імʼям: курс, інструктор або обидва
$about = [];
if ( $course ) { $about[] = 'Курс ' . (string) get_field( 'code', $course ); }
if ( $mate )   { $about[] = 'інструктор ' . get_the_title( $mate ); }
?>
<article class="rev" data-course="<?php echo esc_attr( $course ? (string) get_field( 'code', $course ) : '' ); ?>"
	data-mate="<?php echo esc_attr( (string) $mate ); ?>">
	<div class="rev-who">
		<div class="rev-ava<?php echo has_post_thumbnail( $rid ) ? '' : ' ph'; ?>" aria-hidden="true">
			<?php echo has_post_thumbnail( $rid ) ? get_the_post_thumbnail( $rid, 'thumbnail', [ 'alt' => '', 'loading' => 'lazy' ] ) : ''; ?>
		</div>
		<div>
			<b><?php echo esc_html( get_the_title( $rid ) ); ?></b>
			<?php if ( $about ) : ?>
				<span class="rev-course"><?php echo esc_html( implode( ', ', $about ) ); ?></span>
			<?php endif; ?>
			<span class="stars" role="img" aria-label="Оцінка <?php echo esc_attr( (string) $rate ); ?> з 5">
				<?php for ( $i = 0; $i < $rate; $i++ ) { echo fr_icon( 'i-star', 15 ); } ?>
			</span>
			<?php if ( $given ) : ?>
				<span class="rev-date"><?php echo esc_html( mb_strtolower( (string) wp_date( 'j F Y', strtotime( $given ) ), 'UTF-8' ) ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<div class="rev-text">
		<?php echo fr_icon( 'i-quote', 26 ); ?>
		<p><?php echo esc_html( wp_strip_all_tags( $r->post_content ) ); ?></p>
	</div>
	<?php if ( $shot ) : ?>
		<div class="rev-shot"><?php echo wp_get_attachment_image( $shot, 'fr-card', false, [ 'alt' => '', 'loading' => 'lazy' ] ); ?></div>
	<?php endif; ?>
</article>
