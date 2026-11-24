<?php
/**
 * SEO.
 *
 * Робочий інструмент сеошника — Yoast, ми його не підміняємо. Тут лише
 * те, чого Yoast сам про цей сайт не знає: змінні курсу для шаблонів
 * заголовків і розмітка Course для сніпетів.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Загальна кількість годин курсу — рахується, ніде не зберігається. */
function fr_course_hours( int $post_id ): int {
	$sum = 0;
	foreach ( (array) get_field( 'mix', $post_id ) as $row ) {
		$sum += (int) ( $row['hours'] ?? 0 );
	}
	return $sum;
}

/**
 * Змінні для шаблонів Yoast: сеошник ставить їх у «SEO → Вигляд пошуку →
 * Типи записів → Курси» один раз, і всі курси отримують заголовок
 * і опис автоматично, без ручного заповнення кожного.
 *
 * Приклад шаблону: %%title%% — %%fr_hours%% год, %%fr_price%% грн | %%sitename%%
 */
add_action( 'wpseo_register_extra_replacements', static function (): void {
	if ( ! function_exists( 'wpseo_register_var_replacement' ) ) {
		return;
	}
	wpseo_register_var_replacement( '%%fr_price%%', static function () {
		$id = get_the_ID();
		return $id ? number_format_i18n( (int) get_field( 'price', $id ) ) : '';
	}, 'advanced', 'Ціна курсу, грн' );

	wpseo_register_var_replacement( '%%fr_hours%%', static function () {
		$id = get_the_ID();
		return $id ? (string) fr_course_hours( $id ) : '';
	}, 'advanced', 'Кількість годин у курсі' );

	wpseo_register_var_replacement( '%%fr_code%%', static function () {
		$id = get_the_ID();
		return $id ? (string) get_field( 'code', $id ) : '';
	}, 'advanced', 'Код курсу (STARTER, STREET…)' );
} );

/**
 * Розмітка Course. Дає Google зрозуміти, що це навчальна програма,
 * а не просто сторінка: назва, провайдер, ціна, тривалість.
 */
add_filter( 'wpseo_schema_graph', static function ( $graph, $context ) {
	if ( ! is_singular( FR_COURSE ) || ! is_array( $graph ) ) {
		return $graph;
	}
	$id    = get_the_ID();
	$hours = fr_course_hours( $id );
	$price = (int) get_field( 'price', $id );

	$node = [
		'@type'       => 'Course',
		'@id'         => get_permalink( $id ) . '#course',
		'name'        => get_the_title( $id ),
		'description' => (string) ( get_field( 'summary', $id ) ?: get_the_excerpt( $id ) ),
		'url'         => get_permalink( $id ),
		'provider'    => [
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		],
	];

	if ( $hours > 0 || $price > 0 ) {
		$instance = [
			'@type'              => 'CourseInstance',
			'courseMode'         => 'onsite',
			'courseWorkload'     => $hours > 0 ? 'PT' . $hours . 'H' : null,
		];
		$node['hasCourseInstance'] = [ array_filter( $instance ) ];
	}

	if ( $price > 0 ) {
		$node['offers'] = [ [
			'@type'         => 'Offer',
			'price'         => (string) $price,
			'priceCurrency' => 'UAH',
			'category'      => 'Paid',
			'availability'  => 'https://schema.org/InStock',
			'url'           => get_permalink( $id ),
		] ];
	}

	$graph[] = $node;
	return $graph;
}, 10, 2 );

/**
 * Yoast за замовчуванням не знає, що каталог курсів — це архів типу
 * запису з власною сторінкою. Дозволяємо редагувати його SEO.
 */
add_filter( 'wpseo_title', static function ( $title ) {
	return $title;
} );
