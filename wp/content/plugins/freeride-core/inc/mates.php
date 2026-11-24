<?php
/**
 * Інструктори.
 *
 * Окремий тип запису з тієї ж причини, що й курси: вони показуються
 * і на «Про нас», і на «Команді», а редагуватися мають в одному місці.
 * Інакше два списки неминуче розійдуться.
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const FR_MATE = 'fr_mate';

add_action( 'init', static function (): void {
	register_post_type( FR_MATE, [
		'labels' => [
			'name'          => 'Інструктори',
			'singular_name' => 'Інструктор',
			'add_new'       => 'Додати інструктора',
			'add_new_item'  => 'Новий інструктор',
			'edit_item'     => 'Редагувати інструктора',
			'all_items'     => 'Усі інструктори',
			'menu_name'     => 'Інструктори',
			'not_found'     => 'Інструкторів немає',
		],
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'show_in_rest'    => true,
		'menu_icon'       => 'dashicons-groups',
		'menu_position'   => 22,
		'supports'        => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
		'capability_type' => 'post',
		'has_archive'     => false,
		'rewrite'         => false,
	] );
} );

/**
 * Інструктори в порядку показу.
 *
 * @return WP_Post[]
 */
function fr_mates( int $limit = -1 ): array {
	return get_posts( [
		'post_type'      => FR_MATE,
		'posts_per_page' => $limit,
		'orderby'        => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
		'post_status'    => 'publish',
	] );
}

add_action( 'acf/init', static function (): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group( [
		'key'      => 'group_fr_mate',
		'title'    => 'Дані інструктора',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => FR_MATE ] ] ],
		'fields'   => [
			[ 'key' => 'field_fr_m_role', 'name' => 'role', 'label' => 'Посада', 'type' => 'text', 'required' => 1, 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Наприклад: Головний інструктор' ],
			[ 'key' => 'field_fr_m_exp',  'name' => 'experience', 'label' => 'Досвід', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Наприклад: 10+ років у навчанні' ],
			[ 'key' => 'field_fr_m_quote','name' => 'quote', 'label' => 'Коротко про себе', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '' ],
			[
				'key' => 'field_fr_m_tags', 'name' => 'specialties', 'label' => 'Спеціалізація',
				'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати',
				'sub_fields' => [ [ 'key' => 'field_fr_m_tag', 'name' => 'tag', 'label' => 'Напрям', 'type' => 'text', 'required' => 1 ] ],
			],
		],
	] );
} );

add_filter( 'manage_' . FR_MATE . '_posts_columns', static function ( array $c ): array {
	$new = [];
	foreach ( $c as $k => $v ) {
		if ( 'date' === $k ) {
			$new['fr_role'] = 'Посада';
			$new['fr_exp']  = 'Досвід';
			$new['fr_ord']  = 'Порядок';
		}
		$new[ $k ] = $v;
	}
	return $new;
} );

add_action( 'manage_' . FR_MATE . '_posts_custom_column', static function ( string $col, int $id ): void {
	if ( 'fr_role' === $col ) { echo esc_html( (string) get_field( 'role', $id ) ); }
	if ( 'fr_exp'  === $col ) { echo esc_html( (string) get_field( 'experience', $id ) ); }
	if ( 'fr_ord'  === $col ) { echo (int) get_post_field( 'menu_order', $id ); }
}, 10, 2 );
