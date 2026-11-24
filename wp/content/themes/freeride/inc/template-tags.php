<?php
/**
 * Допоміжні функції шаблонів.
 *
 * Усе, що повторюється на кількох сторінках: іконка, контакти, віджет
 * звʼязку, підвал. Жодного тексту тут не зашито — усе з адмінки,
 * з запасним значенням на випадок порожнього поля.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Іконка зі спрайта. */
function fr_icon( string $id, int $size = 22, string $class = '' ): string {
	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 28 28" aria-hidden="true" focusable="false"%2$s><use href="#%3$s"/></svg>',
		$size,
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		esc_attr( $id )
	);
}

/** Контакти з адмінки одним масивом. */
function fr_contacts(): array {
	static $c = null;
	if ( null !== $c ) {
		return $c;
	}
	$c = [
		'phone'     => (string) ( get_field( 'phone', 'option' ) ?: '' ),
		'phone_raw' => (string) ( get_field( 'phone_raw', 'option' ) ?: '' ),
		'email'     => (string) ( get_field( 'email', 'option' ) ?: '' ),
		'address'   => (string) ( get_field( 'address', 'option' ) ?: '' ),
		'hours'     => (string) ( get_field( 'hours', 'option' ) ?: '' ),
		'map_url'   => (string) ( get_field( 'map_url', 'option' ) ?: '' ),
		'messengers'=> (array) ( get_field( 'messengers', 'option' ) ?: [] ),
		'socials'   => (array) ( get_field( 'socials', 'option' ) ?: [] ),
	];
	return $c;
}

/** Підписи каналів — в одному місці, бо потрібні і у віджеті, і в контактах. */
function fr_channel_labels(): array {
	return [
		'phone' => [ 'i-phone', 'Подзвонити' ],
		'viber' => [ 'i-viber', 'Viber' ],
		'wa'    => [ 'i-wa',    'WhatsApp' ],
		'tg'    => [ 'i-tg',    'Telegram' ],
		'mail'  => [ 'i-mail',  'Написати на пошту' ],
		'ig'    => [ 'i-ig',    'Instagram' ],
		'fb'    => [ 'i-fb',    'Facebook' ],
		'yt'    => [ 'i-yt',    'YouTube' ],
		'tt'    => [ 'i-play',  'TikTok' ],
	];
}

/**
 * Віджет звʼязку — рейка праворуч, вужче 1380px згортається в кнопку.
 * Порожній список месенджерів ховає віджет цілком: краще нічого,
 * ніж кнопка, яка нікуди не веде.
 */
function fr_contact_widget(): void {
	$c = fr_contacts();
	if ( ! $c['messengers'] ) {
		return;
	}
	$labels = fr_channel_labels();
	?>
	<div class="callme" id="callme">
		<button class="callme-toggle" type="button" aria-expanded="false" aria-controls="callme-list">
			<?php echo fr_icon( 'i-phone', 22 ); ?>
			<span class="sr">Звʼязатися з нами</span>
		</button>
		<div class="callme-list" id="callme-list">
			<?php foreach ( $c['messengers'] as $m ) :
				$kind = (string) ( $m['kind'] ?? '' );
				$url  = (string) ( $m['url'] ?? '' );
				if ( ! $url || ! isset( $labels[ $kind ] ) ) {
					continue;
				}
				[ $icon, $label ] = $labels[ $kind ];
				$external = str_starts_with( $url, 'http' );
				?>
				<a href="<?php echo esc_url( $url ); ?>"<?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?>>
					<?php echo fr_icon( $icon, 21 ); ?><span><?php echo esc_html( $label ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/** Логотип. Девіз — опис сайту, редагується в «Налаштування → Загальні». */
function fr_logo( string $class = 'logo' ): void {
	printf(
		'<a class="%s" href="%s"><span><b>%s</b><s>мотошкола</s></span></a>',
		esc_attr( $class ),
		esc_url( home_url( '/' ) ),
		esc_html( get_bloginfo( 'name' ) === 'FreeRide — мотошкола' ? 'FreeRide®' : get_bloginfo( 'name' ) )
	);
}

/** Хлібні крихти. Віддаємо Yoast, якщо він увімкнув свої. */
function fr_breadcrumbs(): void {
	if ( is_front_page() ) {
		return;
	}
	echo '<nav class="crumbs" aria-label="Навігація сторінками">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">Головна</a><span>›</span>';
	if ( is_singular( FR_COURSE ) ) {
		$archive = get_post_type_archive_link( FR_COURSE );
		if ( $archive ) {
			echo '<a href="' . esc_url( $archive ) . '">Курси</a><span>›</span>';
		}
	}
	echo esc_html( wp_strip_all_tags( get_the_title() ) );
	echo '</nav>';
}
