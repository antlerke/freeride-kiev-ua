<?php
/**
 * Картка курсу в каталозі. Та сама розмітка, що в затвердженому макеті,
 * тільки замість вписаних значень — поля курсу.
 *
 * @var WP_Post $post
 */
declare( strict_types = 1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }

$id    = get_the_ID();
$hours = fr_course_hours( $id );
$price = (int) get_field( 'price', $id );
$docs  = (array) ( get_field( 'docs', $id ) ?: [] );
?>
<article class="prog">
	<div class="prog-media<?php echo has_post_thumbnail( $id ) ? '' : ' ph'; ?>">
		<?php
		if ( has_post_thumbnail( $id ) ) {
			echo get_the_post_thumbnail( $id, 'fr-card', [ 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ] );
		} else {
			echo '[ФОТО не додано]';
		}
		?>
		<?php if ( $hours ) : ?>
			<span class="prog-hours"><?php echo fr_icon( 'i-clock', 17 ); ?><?php echo esc_html( $hours ); ?> <?php echo esc_html( _n( 'година', 'годин', $hours, 'freeride' ) ); ?></span>
		<?php endif; ?>
	</div>
	<div class="prog-body">
		<div class="prog-name"><i></i><span>
			<b><?php echo esc_html( (string) get_field( 'code', $id ) ); ?></b>
			<em><?php echo esc_html( get_the_title( $id ) ); ?></em>
		</span></div>

		<?php if ( $lede = get_field( 'summary', $id ) ) : ?>
			<p class="prog-lede"><?php echo esc_html( $lede ); ?></p>
		<?php endif; ?>

		<?php $inc = (array) ( get_field( 'includes', $id ) ?: [] ); ?>
		<?php if ( $inc ) : ?>
			<ul>
				<?php foreach ( $inc as $row ) : ?>
					<?php if ( empty( $row['text'] ) ) { continue; } ?>
					<li><?php echo fr_icon( 'i-check', 19 ); ?><?php echo esc_html( $row['text'] ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="prog-foot">
			<div class="prog-price">
				<?php echo esc_html( number_format_i18n( $price ) ); ?> грн
				<?php if ( $docs ) : ?>
					<s>разом із документами<?php echo count( $docs ) > 1 ? ' А та А1' : ''; ?></s>
				<?php endif; ?>
			</div>
			<a class="btn btn-go" style="font-size: 15px; padding: 12px 18px" href="<?php the_permalink(); ?>">Детальніше
				<?php echo fr_icon( 'i-arrow', 18 ); ?></a>
		</div>
	</div>
</article>
