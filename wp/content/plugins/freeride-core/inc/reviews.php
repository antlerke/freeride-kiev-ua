<?php
/**
 * Відгуки.
 *
 * Один тип запису на все: і відгуки про школу, і відгуки про конкретного
 * інструктора, і відео. Розводити їх по різних сутностях немає сенсу —
 * у них однакові поля, а відрізняються вони лише тим, на кого посилаються.
 *
 * Завдяки цьому відгук, залишений про інструктора, автоматично
 * потрапляє і в загальний список школи.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const FR_REVIEW = 'fr_review';

add_action( 'init', static function (): void {
	register_post_type( FR_REVIEW, [
		'labels' => [
			'name'          => 'Відгуки',
			'singular_name' => 'Відгук',
			'add_new'       => 'Додати відгук',
			'add_new_item'  => 'Новий відгук',
			'edit_item'     => 'Редагувати відгук',
			'all_items'     => 'Усі відгуки',
			'menu_name'     => 'Відгуки',
			'not_found'     => 'Відгуків немає',
			'search_items'  => 'Шукати відгуки',
		],
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'show_in_rest'    => true,
		'menu_icon'       => 'dashicons-format-quote',
		'menu_position'   => 24,
		'supports'        => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
		'capability_type' => 'post',
		'has_archive'     => false,
		'rewrite'         => false,
	] );
} );

/**
 * Відгуки для показу.
 *
 * @param array $a ['kind'=>'text'|'video', 'course'=>id, 'mate'=>id, 'limit'=>n]
 * @return WP_Post[]
 */
function fr_reviews( array $a = [] ): array {
	$args = [
		'post_type'      => FR_REVIEW,
		'post_status'    => 'publish',
		'posts_per_page' => $a['limit'] ?? -1,
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
		'meta_query'     => [],
	];
	if ( ! empty( $a['kind'] ) ) {
		$args['meta_query'][] = [ 'key' => 'kind', 'value' => $a['kind'] ];
	}
	if ( ! empty( $a['course'] ) ) {
		$args['meta_query'][] = [ 'key' => 'course', 'value' => (int) $a['course'] ];
	}
	if ( ! empty( $a['mate'] ) ) {
		$args['meta_query'][] = [ 'key' => 'mate', 'value' => (int) $a['mate'] ];
	}
	return get_posts( $args );
}

/** Середня оцінка й кількість — рахуються, а не вписуються руками. */
function fr_review_score(): array {
	$sum = 0;
	$n   = 0;
	foreach ( fr_reviews() as $r ) {
		$v = (int) get_field( 'rating', $r->ID );
		if ( $v >= 1 && $v <= 5 ) {
			$sum += $v;
			$n++;
		}
	}
	return [
		'count' => $n,
		'avg'   => $n ? round( $sum / $n, 1 ) : 0.0,
	];
}

add_action( 'acf/init', static function (): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group( [
		'key'      => 'group_fr_review',
		'title'    => 'Дані відгуку',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => FR_REVIEW ] ] ],
		'fields'   => [
			[
				'key' => 'field_fr_rv_kind', 'name' => 'kind', 'label' => 'Тип',
				'type' => 'select', 'choices' => [ 'text' => 'Текстовий', 'video' => 'Відео' ],
				'default_value' => 'text', 'wrapper' => [ 'width' => 20 ],
			],
			[
				'key' => 'field_fr_rv_rate', 'name' => 'rating', 'label' => 'Оцінка',
				'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5, 'wrapper' => [ 'width' => 15 ],
			],
			[
				'key' => 'field_fr_rv_date', 'name' => 'given', 'label' => 'Дата відгуку',
				'type' => 'date_picker', 'display_format' => 'j F Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 25 ],
			],
			[
				'key' => 'field_fr_rv_src', 'name' => 'source', 'label' => 'Звідки',
				'type' => 'select', 'wrapper' => [ 'width' => 40 ],
				'choices' => [ 'site' => 'Сайт', 'google' => 'Google', 'fb' => 'Facebook', 'ig' => 'Instagram', 'app' => 'Після заняття' ],
				'default_value' => 'site',
			],
			[
				'key' => 'field_fr_rv_course', 'name' => 'course', 'label' => 'Про курс',
				'type' => 'post_object', 'post_type' => [ FR_COURSE ], 'return_format' => 'id',
				'allow_null' => 1, 'wrapper' => [ 'width' => 50 ],
			],
			[
				'key' => 'field_fr_rv_mate', 'name' => 'mate', 'label' => 'Про інструктора',
				'type' => 'post_object', 'post_type' => [ FR_MATE ], 'return_format' => 'id',
				'allow_null' => 1, 'wrapper' => [ 'width' => 50 ],
				'instructions' => 'Відгук про інструктора показується і в його картці, і в загальному списку школи.',
			],
			[
				'key' => 'field_fr_rv_shot', 'name' => 'photo', 'label' => 'Фото до відгуку',
				'type' => 'image', 'return_format' => 'id', 'wrapper' => [ 'width' => 34 ],
				'instructions' => 'Не портрет автора (це головне зображення), а знімок із заняття.',
			],
			[
				'key' => 'field_fr_rv_vurl', 'name' => 'video_url', 'label' => 'Посилання на відео',
				'type' => 'url', 'wrapper' => [ 'width' => 46 ],
				'conditional_logic' => [ [ [ 'field' => 'field_fr_rv_kind', 'operator' => '==', 'value' => 'video' ] ] ],
			],
			[
				'key' => 'field_fr_rv_vlen', 'name' => 'video_length', 'label' => 'Тривалість',
				'type' => 'text', 'wrapper' => [ 'width' => 20 ],
				'conditional_logic' => [ [ [ 'field' => 'field_fr_rv_kind', 'operator' => '==', 'value' => 'video' ] ] ],
			],
			[
				'key' => 'field_fr_rv_feat', 'name' => 'featured', 'label' => 'Показувати на головній',
				'type' => 'true_false', 'ui' => 1,
			],
		],
	] );
} );

add_filter( 'manage_' . FR_REVIEW . '_posts_columns', static function ( array $c ): array {
	return [
		'cb'        => $c['cb'] ?? '',
		'title'     => 'Хто',
		'fr_rate'   => 'Оцінка',
		'fr_kind'   => 'Тип',
		'fr_about'  => 'Про що',
		'fr_src'    => 'Звідки',
		'date'      => 'Додано',
	];
} );

add_action( 'manage_' . FR_REVIEW . '_posts_custom_column', static function ( string $col, int $id ): void {
	switch ( $col ) {
		case 'fr_rate':
			echo esc_html( str_repeat( '★', max( 0, (int) get_field( 'rating', $id ) ) ) );
			break;
		case 'fr_kind':
			echo 'video' === get_field( 'kind', $id ) ? 'відео' : 'текст';
			break;
		case 'fr_about':
			$parts = [];
			if ( $c = (int) get_field( 'course', $id ) ) { $parts[] = (string) get_field( 'code', $c ); }
			if ( $m = (int) get_field( 'mate', $id ) )   { $parts[] = get_the_title( $m ); }
			echo esc_html( $parts ? implode( ' · ', $parts ) : '—' );
			break;
		case 'fr_src':
			$map = [ 'site' => 'сайт', 'google' => 'Google', 'fb' => 'Facebook', 'ig' => 'Instagram', 'app' => 'після заняття' ];
			echo esc_html( $map[ (string) get_field( 'source', $id ) ] ?? '—' );
			break;
	}
}, 10, 2 );
