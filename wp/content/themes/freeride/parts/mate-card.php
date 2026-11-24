<?php
/**
 * Картка інструктора. Одна на «Про нас» і на «Команду».
 *
 * @var WP_Post $args['mate']
 */
declare( strict_types = 1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }

$m = $args['mate'] ?? null;
if ( ! $m instanceof WP_Post ) {
	return;
}
?>
<article class="mate">
	<div class="mate-photo<?php echo has_post_thumbnail( $m->ID ) ? '' : ' ph'; ?>">
		<?php echo has_post_thumbnail( $m->ID )
			? get_the_post_thumbnail( $m->ID, 'fr-card', [ 'alt' => '', 'loading' => 'lazy' ] )
			: '[ФОТО: інструктор ' . esc_html( get_the_title( $m ) ) . ']'; ?>
	</div>
	<div class="mate-body">
		<div>
			<b><?php echo esc_html( get_the_title( $m ) ); ?></b>
			<span><?php echo esc_html( (string) get_field( 'role', $m->ID ) ); ?></span>
			<?php if ( $e = get_field( 'experience', $m->ID ) ) : ?>
				<span><?php echo esc_html( $e ); ?></span>
			<?php endif; ?>
		</div>
		<span class="mate-go"><?php echo fr_icon( 'i-arrow', 16 ); ?></span>
	</div>
</article>
