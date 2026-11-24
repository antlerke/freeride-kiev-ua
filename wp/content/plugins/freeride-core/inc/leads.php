<?php
/**
 * Заявки з форм.
 *
 * Заявка спершу лягає записом у базу і лише потім іде на пошту.
 * Порядок саме такий навмисно: пошта на хостингу відвалюється
 * регулярно й тихо, і втрачена заявка — це втрачений клієнт.
 * У базі вона лишається в будь-якому разі.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const FR_LEAD = 'fr_lead';

add_action( 'init', static function (): void {
	register_post_type( FR_LEAD, [
		'labels' => [
			'name'          => 'Заявки',
			'singular_name' => 'Заявка',
			'menu_name'     => 'Заявки',
			'all_items'     => 'Усі заявки',
			'edit_item'     => 'Заявка',
			'not_found'     => 'Заявок немає',
		],
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-email-alt',
		'menu_position'       => 23,
		'capability_type'     => 'post',
		'capabilities'        => [ 'create_posts' => 'do_not_allow' ],
		'map_meta_cap'        => true,
		'supports'            => [ 'title' ],
		'has_archive'         => false,
		'rewrite'             => false,
		'exclude_from_search' => true,
	] );
} );

/** Поля заявки видно прямо в списку — відкривати кожну не треба. */
add_filter( 'manage_' . FR_LEAD . '_posts_columns', static function ( array $c ): array {
	return [
		'cb'       => $c['cb'] ?? '',
		'title'    => 'Хто',
		'fr_phone' => 'Телефон',
		'fr_topic' => 'Тема',
		'fr_msg'   => 'Повідомлення',
		'fr_sent'  => 'Пошта',
		'date'     => 'Коли',
	];
} );

add_action( 'manage_' . FR_LEAD . '_posts_custom_column', static function ( string $col, int $id ): void {
	switch ( $col ) {
		case 'fr_phone':
			$p = (string) get_post_meta( $id, 'phone', true );
			echo $p ? '<a href="tel:' . esc_attr( $p ) . '">' . esc_html( $p ) . '</a>' : '—';
			break;
		case 'fr_topic':
			echo esc_html( (string) get_post_meta( $id, 'topic', true ) ?: '—' );
			break;
		case 'fr_msg':
			echo esc_html( wp_trim_words( (string) get_post_meta( $id, 'message', true ), 14 ) );
			break;
		case 'fr_sent':
			echo get_post_meta( $id, 'mail_sent', true )
				? '<span title="Лист надіслано">✓</span>'
				: '<span style="color:#b32d2e" title="Лист не пішов — заявку видно лише тут">не пішла</span>';
			break;
	}
}, 10, 2 );

/**
 * Приймання форми.
 *
 * Захист — nonce плюс приманка: поле, якого людина не бачить і не
 * заповнює, а робот заповнює майже завжди. Капчі немає навмисно:
 * вона псує конверсію на тому самому кроці, заради якого існує сторінка.
 */
add_action( 'admin_post_nopriv_fr_lead', 'fr_handle_lead' );
add_action( 'admin_post_fr_lead', 'fr_handle_lead' );

function fr_handle_lead(): void {
	$back = wp_get_referer() ?: home_url( '/' );

	if ( ! isset( $_POST['fr_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['fr_nonce'] ), 'fr_lead' ) ) {
		wp_safe_redirect( add_query_arg( 'fr', 'error', $back ) . '#form' );
		exit;
	}
	// Приманка заповнена — це робот. Відповідаємо як на успіх, щоб не вчився.
	if ( ! empty( $_POST['fr_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'fr', 'ok', $back ) . '#form' );
		exit;
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['fr_name'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['fr_phone'] ?? '' ) );
	$topic   = sanitize_text_field( wp_unslash( $_POST['fr_topic'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['fr_message'] ?? '' ) );

	// Телефон обовʼязковий: без нього заявка нічого не варта
	if ( '' === $phone || strlen( preg_replace( '/\D/', '', $phone ) ) < 9 ) {
		wp_safe_redirect( add_query_arg( 'fr', 'phone', $back ) . '#form' );
		exit;
	}

	$id = wp_insert_post( [
		'post_type'   => FR_LEAD,
		'post_status' => 'publish',
		'post_title'  => $name !== '' ? $name : 'Без імені',
	], true );

	if ( is_wp_error( $id ) ) {
		wp_safe_redirect( add_query_arg( 'fr', 'error', $back ) . '#form' );
		exit;
	}

	update_post_meta( $id, 'phone', $phone );
	update_post_meta( $id, 'topic', $topic );
	update_post_meta( $id, 'message', $message );
	update_post_meta( $id, 'source', esc_url_raw( $back ) );

	$to = (string) ( get_field( 'form_recipient', 'option' ) ?: get_option( 'admin_email' ) );
	$ok = wp_mail(
		$to,
		'Заявка з сайту: ' . ( $name ?: 'без імені' ),
		implode( "\n", array_filter( [
			'Імʼя: ' . ( $name ?: '—' ),
			'Телефон: ' . $phone,
			$topic ? 'Тема: ' . $topic : '',
			$message ? "Повідомлення:\n" . $message : '',
			'',
			'Сторінка: ' . $back,
			'У адмінці: ' . admin_url( 'post.php?post=' . $id . '&action=edit' ),
		] ) ),
		[ 'Content-Type: text/plain; charset=UTF-8' ]
	);
	update_post_meta( $id, 'mail_sent', $ok ? '1' : '' );

	wp_safe_redirect( add_query_arg( 'fr', 'ok', $back ) . '#form' );
	exit;
}

/** Вміст заявки — на сторінці редагування, щоб було де прочитати цілком. */
add_action( 'add_meta_boxes', static function (): void {
	add_meta_box( 'fr_lead_box', 'Заявка', static function ( WP_Post $p ): void {
		$rows = [
			'Телефон'      => get_post_meta( $p->ID, 'phone', true ),
			'Тема'         => get_post_meta( $p->ID, 'topic', true ),
			'Повідомлення' => get_post_meta( $p->ID, 'message', true ),
			'Звідки'       => get_post_meta( $p->ID, 'source', true ),
		];
		echo '<table class="widefat striped"><tbody>';
		foreach ( $rows as $k => $v ) {
			printf(
				'<tr><th style="width:140px">%s</th><td>%s</td></tr>',
				esc_html( $k ),
				nl2br( esc_html( (string) $v ) ) ?: '—'
			);
		}
		echo '</tbody></table>';
	}, FR_LEAD, 'normal', 'high' );
} );
