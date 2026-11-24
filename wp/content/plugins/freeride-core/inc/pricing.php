<?php
/**
 * Прайс одним масивом.
 *
 * Калькулятор на сайті вже вміє читати цю схему — вона один в один
 * повторює `docs/data/pricing.json`, з якого його будували. Тому
 * перехід на адмінку не потребує жодної правки в JS: міняється лише
 * адреса, звідки беруться дані.
 *
 * Жодне число тут не прописане. Усе приходить із типу запису «Курс»
 * і зі сторінки «Курси → Ціни».
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const FR_PRICING_CACHE = 'fr_pricing_payload';

function fr_pricing_payload(): array {
	$cached = get_transient( FR_PRICING_CACHE );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$types = [];
	foreach ( (array) get_field( 'lesson_types', 'option' ) as $row ) {
		if ( empty( $row['id'] ) ) {
			continue;
		}
		$types[] = [
			'id'    => (string) $row['id'],
			'label' => (string) ( $row['label'] ?? $row['id'] ),
			'icon'  => (string) ( $row['icon'] ?? 'i-moto' ),
		];
	}

	$courses = [];
	foreach ( fr_courses( true ) as $post ) {
		$mix = [];
		foreach ( (array) get_field( 'mix', $post->ID ) as $row ) {
			if ( empty( $row['type'] ) ) {
				continue;
			}
			// Один тип може трапитися двічі — підсумовуємо, а не затираємо
			$mix[ $row['type'] ] = ( $mix[ $row['type'] ] ?? 0 ) + (int) ( $row['hours'] ?? 0 );
		}
		$courses[] = [
			'id'     => $post->post_name,
			'name'   => (string) ( get_field( 'code', $post->ID ) ?: $post->post_title ),
			'legacy' => (string) ( get_field( 'legacy', $post->ID ) ?: '' ),
			'icon'   => (string) ( get_field( 'icon', $post->ID ) ?: 'i-cone' ),
			'price'  => (int) get_field( 'price', $post->ID ),
			'mix'    => (object) $mix,
			'docs'   => array_values( (array) ( get_field( 'docs', $post->ID ) ?: [] ) ),
			'url'    => get_permalink( $post ),
		];
	}

	$extras = [];
	foreach ( (array) get_field( 'extras', 'option' ) as $row ) {
		if ( empty( $row['type'] ) ) {
			continue;
		}
		$extras[] = [
			'id'    => (string) $row['type'],
			'type'  => (string) $row['type'],
			'label' => (string) ( $row['label'] ?? '' ),
			'rate'  => (int) ( $row['rate'] ?? 0 ),
			'max'   => (int) ( $row['max'] ?? 20 ),
		];
	}

	$documents = [];
	$doc_labels = [ 'A' => 'А', 'A1' => 'А1' ];
	foreach ( (array) get_field( 'documents', 'option' ) as $row ) {
		if ( empty( $row['id'] ) ) {
			continue;
		}
		$documents[] = [
			'id'    => (string) $row['id'],
			'label' => (string) ( $row['label'] ?? '' ),
			'price' => (int) ( $row['price'] ?? 0 ),
			'note'  => (string) ( $row['note'] ?? '' ),
		];
		$doc_labels[ (string) $row['id'] ] = (string) ( $row['label'] ?? $row['id'] );
	}

	// Порівняння посилається на курс слагом, а не числовим ID: у JSON
	// він має збігатися з courses[].id, інакше калькулятор його не знайде.
	$target_id = (int) get_field( 'compare_target', 'option' );
	$target    = $target_id ? get_post( $target_id ) : null;

	$payload = [
		'_'           => 'Згенеровано FreeRide Core з даних адмінки. Редагувати тут нічого не треба.',
		'version'     => (string) get_option( 'fr_pricing_version', '1' ),
		'currency'    => 'грн',
		'unit'        => 'год',
		'lessonTypes' => $types,
		'courses'     => $courses,
		'extras'      => $extras,
		'documents'   => $documents,
		'docLabels'   => (object) $doc_labels,
		'compare'     => [
			'enabled' => (bool) get_field( 'compare_enabled', 'option' ) && $target instanceof WP_Post,
			'target'  => $target instanceof WP_Post ? $target->post_name : '',
		],
		'custom'      => [
			'enabled' => (bool) get_field( 'custom_enabled', 'option' ),
			'id'      => 'custom',
			'name'    => (string) ( get_field( 'custom_name', 'option' ) ?: 'Лише окремі заняття' ),
			'badge'   => (string) ( get_field( 'custom_badge', 'option' ) ?: 'БЕЗ ПАКЕТА' ),
			'note'    => (string) ( get_field( 'custom_note', 'option' ) ?: '' ),
			'cta'     => (string) ( get_field( 'custom_cta', 'option' ) ?: 'Записатися на заняття' ),
			'icon'    => 'i-layers',
		],
	];

	set_transient( FR_PRICING_CACHE, $payload, DAY_IN_SECONDS );
	return $payload;
}

/** Будь-яка правка курсу чи прайсу скидає кеш — інакше ціни розійдуться. */
function fr_flush_pricing(): void {
	delete_transient( FR_PRICING_CACHE );
	update_option( 'fr_pricing_version', (string) time(), false );
}
add_action( 'save_post_' . FR_COURSE, 'fr_flush_pricing' );
add_action( 'deleted_post', 'fr_flush_pricing' );
add_action( 'acf/save_post', static function ( $id ): void {
	if ( 'options' === $id || get_post_type( $id ) === FR_COURSE ) {
		fr_flush_pricing();
	}
}, 20 );

add_action( 'rest_api_init', static function (): void {
	register_rest_route( 'freeride/v1', '/pricing', [
		'methods'             => 'GET',
		'permission_callback' => '__return_true',
		'callback'            => static function () {
			$r = new WP_REST_Response( fr_pricing_payload() );
			// Версія в адресі міняється при кожній правці, тож кешувати
			// можна надовго і не боятися застарілих цін.
			$r->header( 'Cache-Control', 'public, max-age=300' );
			return $r;
		},
	] );
} );
