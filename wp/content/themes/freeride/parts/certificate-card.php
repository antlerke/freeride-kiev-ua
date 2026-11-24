<?php
/**
 * Сам сертифікат — намальований, а не сфотографований.
 *
 * Фото сертифіката довелося б перезнімати при кожній зміні номіналу
 * чи логотипа. Тут усе змінне — з полів, а вигляд тримає CSS.
 *
 * @var array $args ['caption','note','term','value']
 */
declare( strict_types = 1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="cert" role="img" aria-label="<?php echo esc_attr( trim( ( $args['caption'] ?? '' ) . ' ' . ( $args['value'] ?? '' ) ) ); ?>">
	<div class="cert-card">
		<div class="cert-sheen" aria-hidden="true"></div>
		<div class="cert-top">
			<span class="cert-logo"><b>FreeRide®</b><s>мотошкола</s></span>
			<?php echo fr_icon( 'i-barshield', 34, 'cert-mark' ); ?>
		</div>
		<div class="cert-mid">
			<p class="cert-cap"><?php echo esc_html( (string) ( $args['caption'] ?? '' ) ); ?></p>
			<?php if ( ! empty( $args['value'] ) ) : ?>
				<p class="cert-val"><?php echo esc_html( (string) $args['value'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="cert-bot">
			<span><?php echo esc_html( (string) ( $args['note'] ?? '' ) ); ?></span>
			<?php if ( ! empty( $args['term'] ) ) : ?>
				<span class="cert-term"><?php echo esc_html( (string) $args['term'] ); ?></span>
			<?php endif; ?>
		</div>
		<span class="cert-punch" aria-hidden="true"></span>
	</div>
	<div class="cert-envelope" aria-hidden="true"></div>
</div>
