<?php
/**
 * Форма заявки. Одна на всі сторінки, де щось замовляють.
 *
 * @var array $args ['title'=>..., 'intro'=>..., 'button'=>..., 'topic'=>фіксована тема, 'topics'=>масив тем]
 */
declare( strict_types = 1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }

$title  = (string) ( $args['title']  ?? 'Звʼяжіться з нами' );
$intro  = (string) ( $args['intro']  ?? '' );
$button = (string) ( $args['button'] ?? 'Надіслати запит' );
$fixed  = (string) ( $args['topic']  ?? '' );
$topics = (array)  ( $args['topics'] ?? [] );
$ok     = (string) ( $args['success'] ?? 'Дякуємо! Передзвонимо найближчим часом.' );
$msg    = isset( $_GET['fr'] ) ? sanitize_key( (string) $_GET['fr'] ) : '';
?>
<div class="fcard" id="form">
	<h2 class="eyebrow-h"><span class="slash sm"><i></i><i></i></span><?php echo esc_html( $title ); ?></h2>
	<?php if ( $intro ) : ?>
		<p class="fcard-intro"><?php echo esc_html( $intro ); ?></p>
	<?php endif; ?>

	<?php if ( 'ok' === $msg ) : ?>
		<p class="fnote is-ok" role="status"><?php echo esc_html( $ok ); ?></p>
	<?php elseif ( 'phone' === $msg ) : ?>
		<p class="fnote is-err" role="alert">Перевірте номер телефону — без нього ми не зможемо передзвонити.</p>
	<?php elseif ( 'error' === $msg ) : ?>
		<p class="fnote is-err" role="alert">Не вдалося надіслати. Спробуйте ще раз або подзвоніть нам.</p>
	<?php endif; ?>

	<form class="fform" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="fr_lead">
		<?php wp_nonce_field( 'fr_lead', 'fr_nonce' ); ?>
		<?php // Приманка для роботів: людина цього поля не бачить ?>
		<div class="sr" aria-hidden="true">
			<label>Не заповнюйте це поле<input type="text" name="fr_website" tabindex="-1" autocomplete="off"></label>
		</div>
		<?php if ( $fixed ) : ?>
			<input type="hidden" name="fr_topic" value="<?php echo esc_attr( $fixed ); ?>">
		<?php endif; ?>

		<label class="fld">
			<span class="fld-ic"><?php echo fr_icon( 'i-people', 19 ); ?></span>
			<input type="text" name="fr_name" placeholder="Ваше імʼя" autocomplete="name">
		</label>

		<label class="fld">
			<span class="fld-ic"><?php echo fr_icon( 'i-phone', 19 ); ?></span>
			<input type="tel" name="fr_phone" placeholder="+38 (___) ___ __ __" required autocomplete="tel">
		</label>

		<?php if ( ! $fixed && $topics ) : ?>
			<label class="fld">
				<span class="fld-ic"><?php echo fr_icon( 'i-moto', 19 ); ?></span>
				<select name="fr_topic">
					<option value="">Тема запиту</option>
					<?php foreach ( $topics as $t ) : ?>
						<?php $t = is_array( $t ) ? ( $t['topic'] ?? '' ) : $t; ?>
						<?php if ( ! $t ) { continue; } ?>
						<option value="<?php echo esc_attr( $t ); ?>"><?php echo esc_html( $t ); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="fld-caret" aria-hidden="true">▾</span>
			</label>
		<?php endif; ?>

		<label class="fld fld-area">
			<span class="fld-ic"><?php echo fr_icon( 'i-quote', 19 ); ?></span>
			<textarea name="fr_message" rows="3" placeholder="Ваше повідомлення (за бажанням)"></textarea>
		</label>

		<button class="btn btn-go btn-xl fform-go" type="submit">
			<?php echo fr_icon( 'i-arrow', 19 ); ?>
			<span><b><?php echo esc_html( $button ); ?></b></span>
		</button>
	</form>
</div>
